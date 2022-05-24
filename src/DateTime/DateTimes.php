<?php
declare(strict_types=1);

namespace App\DateTime;

use DateTime;
use DateTimeZone;

/**
 * Classe permettant de récupérer une datetime en prenant en compte la TimeZone configurée dans le .env
 */
final class DateTimes
{
    /**
     * @return string Retour la TimeZone
     */
    public static function getTimeZone(): string
    {
        return $_ENV["TIMEZONE"];
    }

    /**
     * @param DateTime|null $dateTime
     * @return DateTime Retour l'instance DatimeTime en prenant en compte la TimeZone
     */
    public static function getDateTime(DateTime $dateTime = null): DateTime
    {
        if (!$dateTime) {
            $dateTime = new DateTime();
        }
        $dateTime->setTimezone(new DateTimeZone(self::getTimeZone()));
        return $dateTime;

    }

    public static function getPreviousWeek(int $nbWeek = 1, DateTime $dateTime = null): array
    {
        if (!$dateTime) {
            $dateTime = self::getDateTime();
        }
        $weekday = $dateTime->format('w');
        $diff = (7 * $nbWeek) + ($weekday == 0 ? 6 : $weekday - 1); // Monday=0, Sunday=6
        $between["start"] = $dateTime->modify("-$diff day")->format("Y-m-d");
        $between["end"] = $dateTime->modify('+6 day')->format("Y-m-d");

        return $between;
    }

}