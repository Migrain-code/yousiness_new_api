<?php
namespace App\Services;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class GoogleCalender
{
    protected $client;
    protected $service;

    public function __construct()
    {
        // Google Client oluşturma ve kimlik doğrulama ayarları
        $this->client = new Client();
        $this->client->setAuthConfig([
            'web' => [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            ],
        ]);
        $this->client->setScopes([Calendar::CALENDAR_EVENTS]);

        // Google Service oluşturma
        $this->service = new Calendar($this->client);
    }

    public function createEvent($summary, $description, $startDateTime, $endDateTime)
    {
        // Etkinlik nesnesi oluşturma
        $event = new Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'dateTime' => $startDateTime,
                'timeZone' => 'Europe/Istanbul',
            ],
            'end' => [
                'dateTime' => $endDateTime,
                'timeZone' => 'Europe/Istanbul',
            ],
        ]);

        // Takvim ID'sini ayarlama (örneğin, varsayılan takvim)
        $calendarId = 'primary';

        // Etkinliği eklemek
        $event = $this->service->events->insert($calendarId, $event);

        // Etkinlik ekledikten sonra başka bir işlem yapabilirsiniz
        return $event;
    }
}
