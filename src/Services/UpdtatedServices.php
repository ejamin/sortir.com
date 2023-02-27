<?php

namespace App\Services;

use App\Entity\Etats;
use App\Entity\Sorties;
use App\Repository\EtatsRepository;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdtatedServices
{
//
//    private $etats;
//    private $entityManager;
//    private EtatsRepository $etatsRepository;
//    private SortiesRepository $sortiesRepository;
//
//    public function __construct(EntityManagerInterface $entityManager,EtatsRepository $etatsRepository,SortiesRepository $sortiesRepository)
//    {
//        $this->entityManager = $entityManager;
//        $this->etatsRepository = $etatsRepository;
//        $this->sortiesRepository = $sortiesRepository;
//        $this->etats = $this->entityManager->getRepository(Etats::class)->findAll();
//
//    }
//
//    public function updateEtat() #Créér - Ouverte - Clôturée - Activitée en cours - Passée - Annulée - Archivée
//    {
//        $sorties = $this->sortiesRepository->findAll();
//        foreach ($sorties as $sortie)
//        {
//            switch($sortie->getIdEtat()) {
//
//            case $this->etats['Ouverte']:
//                {
//                    $this->opened($sortie);
//                    break;
//                }
//                case $this->etats['Clôturée']:
//                {
//                    $this->closed($sortie);
//                    break;
//                }
//                case $this->etats['Activitée en cours']:
//                {
//                    $this->inProgress($sortie);
//                    break;
//                }
//                case $this->etats['Passée']:
//                {
//                    $this->finished($sortie);
//                    break;
//                }
//                case $this->etats['Annulée']:
//                {
//                    $this->canceled($sortie);
//                    break;
//                }
//                case $this->etats['Archivée']:
//                {
//                    $this->archived($sortie);
//                    break;
//                }
//            }
//            return $sortie;
//        }
//
//
//
//
//    }
//
//
//
//    private function opened(Sorties $sortie)
//    {
//        $nbParticipant       = $sortie->getIdParticipant()->count();
//        $max      = $sortie->getNbInscritMax();
//        $dateFin     = $sortie->getDateFin();
//        $now                     = new \DateTime;
//
//
//
//        if($this->eventIsCancelled())
//        {
//            $sortie->setState($this->etats['Activité annulée']);
//
//        }
//        else if($this->registrationAreClosed($nbParticipant,
//            $max,
//            $dateFin,
//            $now))
//        {
//
//
//            $sortie->setState($this->etats['Clôturée']);
//        }
//    }
//
//    private function closed(Sorties $sortie)
//    {
//        $nbParticipant       = $sortie->getParticipants()->count();
//        $max      = $sortie->getMaxRegistration();
//
//        $dateEventBegin          = $sortie->getDateBegin();
//        $dateFin     = $sortie->getDateEnd();
//        $now                     = new \DateTime;
//        $duration = $sortie->getDuration();
//        $dateEventEnd   = \DateTimeImmutable::createFromMutable($dateEventBegin)->modify("+ $duration day");
//
//        if($this->eventIsCancelled())
//        {
//            $sortie->setState($this->etats['Activité annulée']);
//
//        }
//        else if($this->eventIsOngoing($dateEventBegin, $now, $dateEventEnd))
//        {
//            $sortie->setState($this->etats['Activité en cours']);
//        }
//        else if($this->registrationAreOpened($nbParticipant,
//            $max,
//            $dateEventBegin,
//            $now,
//            $dateFin,
//            $dateEventEnd))
//        {
//            $sortie->setState($this->etats['Ouverte']);
//        }
//    }
//
//    private function inProgress(Sorties $sortie)
//    {
//        $dateEventBegin          = $sortie->getDateBegin();
//        $now                     = new \DateTime;
//        $duration = $sortie->getDuration();
//        $dateEventEnd   = \DateTimeImmutable::createFromMutable($dateEventBegin)->modify("+ $duration day");
//
//        if($this->eventIsFinished( $dateEventBegin, $now,  $dateEventEnd))
//        {
//            $sortie->setState($this->etats['Activité passée']);
//        }
//    }
//
//    private function finished(Sorties $sortie)
//    {
//        $dateEventBegin          = $sortie->getDateBegin();
//        $now                     = new \DateTime;
//        $currentState= $sortie->getState()->getId();
//        $duration = $sortie->getDuration();
//        $dateEventEnd   = \DateTimeImmutable::createFromMutable($dateEventBegin)->modify("+ $duration day");
//
//        if($this->eventShallBeHistorized($dateEventBegin,  $now, $dateEventEnd, $currentState))
//        {
//            $sortie->setState($this->etats['Activité historisée']);
//        }
//    }
//
//    private function canceled(Sorties $sortie)
//    {
//        $dateEventBegin          = $sortie->getDateBegin();
//        $now                     = new \DateTime;
//        $currentState= $sortie->getState()->getId();
//        $duration = $sortie->getDuration();
//        $dateEventEnd   = \DateTimeImmutable::createFromMutable($dateEventBegin)->modify("+ $duration day");
//
//        if($this->eventShallBeHistorized($dateEventBegin,  $now,  $dateEventEnd, $currentState))
//        {
//            $sortie->setState($this->etats['Activité historisée']);
//        }
//    }
//
//
//    /**
//     * @param \DateTimeInterface|null $dateEventBegin
//     * @param \DateTime $now
//     * @param \DateTimeImmutable $dateEventEnd
//     * @return bool
//     */
//    private function eventIsFinished( \DateTimeInterface $dateEventBegin, \DateTime $now,  \DateTimeImmutable $dateEventEnd): bool
//    {
//        return $dateEventBegin < $now
//            && $dateEventEnd < $now;
//    }
//
//    /**
//     * @param int $nbParticipant
//     * @param int|null $max
//     * @param \DateTimeInterface|null $dateFin
//     * @param \DateTime $now
//     * @return bool
//     */
//    private function registrationAreClosed(int $nbParticipant,
//                                           int $max,
//                                           \DateTimeInterface $dateFin,
//                                           \DateTime $now): bool
//    {
//        return ($nbParticipant >= $max)
//            || ($dateFin < $now);
//    }
//
//    /**
//     * @param \DateTimeInterface|null $dateEventBegin
//     * @param \DateTime $now
//     * @param \DateTimeImmutable $dateEventEnd
//     * @return bool
//     */
//    private function eventIsOngoing( \DateTimeInterface $dateEventBegin,\DateTime $now, \DateTimeImmutable $dateEventEnd): bool
//    {
//        return $dateEventBegin <= $now
//            && $dateEventEnd > $now;
//    }
//
//    /**
//     * @param int $nbParticipant
//     * @param int|null $max
//     * @param \DateTimeInterface|null $dateEventBegin
//     * @param \DateTime $now
//     * @param \DateTimeInterface|null $dateFin
//     * @param \DateTimeImmutable $dateEventEnd
//     * @return bool
//     */
//    private function registrationAreOpened(int $nbParticipant, int $max,\DateTimeInterface $dateEventBegin, \DateTime $now, \DateTimeInterface $dateFin, \DateTimeImmutable $dateEventEnd): bool
//    {
//        return $nbParticipant < $max
//            && $dateEventBegin > $now
//            && $dateFin > $now
//            && $dateEventEnd > $now;
//    }
//
//
//    /**
//     * @param \DateTimeInterface|null $dateEventBegin
//     * @param \DateTime $now
//     * @param \DateTimeImmutable $dateEventEnd
//     * @return bool
//     */
//    private function eventShallBeHistorized(\DateTimeInterface $dateEventBegin, \DateTime $now, \DateTimeImmutable $dateEventEnd, $currentState): bool
//    {
//        $isHistorized = false;
//
//        $newDateEventBegin= \DateTimeImmutable::createFromMutable($dateEventBegin)->modify("+1 Month") ;
//        if($currentState=== 5 && $dateEventEnd->modify("+1 Month") < $now)
//        {
//            $isHistorized= true;
//
//        }
//
//        elseif ($currentState=== 6 && $newDateEventBegin < $now)
//        {
//            $isHistorized= true;
//        }
//
//        return $isHistorized;
//    }
//
//
//
//    private function eventIsCancelled(): bool
//    {
//        return false;
//    }
}