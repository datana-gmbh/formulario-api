<?php

declare(strict_types=1);

/**
 * This file is part of Datapool-Api.
 *
 * (c) Datana GmbH <info@datana.rocks>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Datana\Datapool\Api;

use Datana\Datapool\Api\Domain\Value\DatapoolId;
use Datana\Datapool\Api\Response\AktenResponse;
use Datana\Datapool\Api\Response\ETerminInfoResponse;
use Datana\Datapool\Api\Response\KtAktenInfoResponse;
use Datana\Datapool\Api\Response\SachstandResponse;
use Datana\Datapool\Api\Response\SimplyBookInfoResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Oskar Stark <oskar.stark@googlemail.de>
 */
interface AktenApiInterface
{
    public function getById(DatapoolId $datapoolId): ResponseInterface;

    public function getByAktenzeichen(string $aktenzeichen): ResponseInterface;

    public function getOneByAktenzeichen(string $aktenzeichen): AktenResponse;

    public function getETerminInfo(DatapoolId $datapoolId): ETerminInfoResponse;

    public function getSimplyBookInfo(DatapoolId $datapoolId): SimplyBookInfoResponse;

    public function getKtAktenInfo(DatapoolId $datapoolId): KtAktenInfoResponse;

    public function getSachstand(DatapoolId $datapoolId): SachstandResponse;

    public function search(string $searchTerm): ResponseInterface;

    public function getByFahrzeugIdentifikationsnummer(string $fahrzeugIdentifikationsnummer): ResponseInterface;

    /**
     * Diese Methode setzt "Ja" in KT beim Feld "Nutzer Mandantencockpit", das bedeutet,
     * dass nur noch das Mandantencockpit für die Benachrichtigungen an den User zuständig ist.
     *
     * Andere Systeme wie KT, Formulario, VWV senden dann keine E-Mails oder SMS mehr an den Mandanten!
     */
    public function setValueNutzerMandantencockpit(DatapoolId $datapoolId, bool $value): bool;
}
