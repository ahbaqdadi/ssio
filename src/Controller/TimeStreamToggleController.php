<?php

namespace App\Controller;

use App\Form\TimeStreamToggleFormType;
use App\Form\TimeStreamFormType;
use App\Message\Command\CreateTimeStreamCommand;
use App\Message\Command\StopTimeStreamCommand;
use App\Message\Command\EditTimeStreamCommand;
use App\Message\Command\DeleteTimeStreamCommand;
use App\Repository\TimeStreamRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Message\Query\GetListTimeStreamQuery;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\HttpFoundation\StreamedResponse;


class TimeStreamToggleController extends AbstractController
{

    public function index(Request $request, MessageBusInterface $messageBus, TimeStreamRepository $repository)
    {
        $form = $this->createForm(TimeStreamToggleFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('toggle')->isClicked()) {
                // Check if TimeStream is running for user
                $userId = 1;
                $runningTimeStream = $repository->findRunningForUser($userId);
                
                if ($runningTimeStream && $runningTimeStream->getIsStartTime()) {
                    // Dispatch StopTimeStreamCommand
                    $messageBus->dispatch(new StopTimeStreamCommand($runningTimeStream));
                } else {
                    // Dispatch CreateTimeStreamCommand
                    $messageBus->dispatch(new CreateTimeStreamCommand($userId));
                }
            }
        }

        $list = new GetListTimeStreamQuery();
        $envelope = $messageBus->dispatch($list);
        $timeStream = $envelope->last(HandledStamp::class)->getResult();
        
        return $this->render('time_stream_toggle/index.html.twig', [
            'form' => $form->createView(),
            'listTimeStream' => $timeStream
        ]);
    }


    public function edit(int $id, Request $request, MessageBusInterface $messageBus, TimeStreamRepository $repository)
    {
        $timeStream = $repository->find($id);

        if (!$timeStream) {
            throw $this->createNotFoundException('No TimeStream found for id '.$id);
        }

        $form = $this->createForm(TimeStreamFormType::class, $timeStream);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Dispatch EditTimeStreamCommand
            $messageBus->dispatch(new EditTimeStreamCommand($id, $form->get('startTime')->getData(), $form->get('endTime')->getData(), $form->get('isStartTime')->getData()));

            return $this->redirectToRoute('timestream_toggle');
        }

        return $this->render('time_stream_toggle/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function delete(int $id, MessageBusInterface $messageBus, TimeStreamRepository $repository)
    {
        $timeStream = $repository->find($id);

        if (!$timeStream) {
            throw $this->createNotFoundException('No TimeStream found for id '.$id);
        }

        // Dispatch DeleteTimeStreamCommand
        $messageBus->dispatch(new DeleteTimeStreamCommand($id));

        return $this->redirectToRoute('timestream_toggle');
    }


    public function exportCsv(MessageBusInterface $messageBus)
    {
        $list = new GetListTimeStreamQuery();
        $envelope = $messageBus->dispatch($list);
        $timeStreamList = $envelope->last(HandledStamp::class)->getResult();

        $csv = fopen('php://temp', 'r+');

        // Header
        fputcsv($csv, ['Id', 'UserId', 'Start Time', 'End Time', 'Is Start Time']);

        // Content
        foreach ($timeStreamList as $timeStream) {
            $row = [
                $timeStream->getId(),
                $timeStream->getUserId(),
                $timeStream->getStartTime() ? $timeStream->getStartTime()->format('Y-m-d H:i:s') : '',
                $timeStream->getEndTime() ? $timeStream->getEndTime()->format('Y-m-d H:i:s') : '',
                $timeStream->getIsStartTime() ? 'Start' : 'End',
            ];
            fputcsv($csv, $row);
        }

        rewind($csv);
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="TimeStream.csv"');

        $response->setCallback(function () use ($csv) {
        fpassthru($csv);
        });

        return $response;
    }
}