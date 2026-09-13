<?php

namespace App\Libraries\SSO;

use GuzzleHttp\Exception\ClientException;

/**
 * Calendar Model - Microsoft Graph API
 * Supports: read events, add events
 */
class Kalender extends BaseModel
{
    public $data;

    /**
     * Fetch calendar events for the next N days.
     */
    public function fetchEvents(int $days = 7): array
    {
        $start = date('Y-m-d\TH:i:s\Z');
        $end = (new \DateTime("+{$days} days"))->format('Y-m-d\TH:i:s\Z');

        $url = '/me/calendarview?'
            . 'startDateTime=' . $start
            . '&endDateTime=' . $end
            . '&$select=subject,start,end,webLink,location,isOnlineMeeting,onlineMeetingUrl'
            . '&$orderby=start/dateTime';

        try {
            $msg = $this->graph()->createRequest("get", $url)->execute();
            $this->data = $msg->getBody()['value'] ?? [];
        } catch (ClientException $e) {
            throw new \Exception("Gagal mengambil kalender. Pastikan user memberikan izin Calendars.Read.", 1);
        }

        return $this->data;
    }

    /**
     * Add a new event to the user's primary calendar.
     *
     * @param string $subject   Event title
     * @param string $start     Start datetime (ISO 8601, e.g. "2026-09-15T09:00:00")
     * @param string $end       End datetime (ISO 8601)
     * @param string $body      Event description (optional)
     * @param string $location  Location (optional)
     * @param array  $attendees Array of email addresses to invite (optional)
     * @param bool   $isOnline  Whether it's an online meeting (optional)
     * @return array            Created event data from Microsoft Graph
     */
    public function addEvent(
        string $subject,
        string $start,
        string $end,
        string $body = '',
        string $location = '',
        array $attendees = [],
        bool $isOnline = false
    ): array {
        $event = [
            'subject' => $subject,
            'start' => [
                'dateTime' => $start,
                'timeZone' => 'SE Asia Standard Time',
            ],
            'end' => [
                'dateTime' => $end,
                'timeZone' => 'SE Asia Standard Time',
            ],
        ];

        if ($body !== '') {
            $event['body'] = [
                'contentType' => 'HTML',
                'content' => $body,
            ];
        }

        if ($location !== '') {
            $event['location'] = [
                'displayName' => $location,
            ];
        }

        if (!empty($attendees)) {
            $event['attendees'] = array_map(fn($email) => [
                'emailAddress' => [
                    'address' => $email,
                    'name' => $email,
                ],
                'type' => 'Required',
            ], $attendees);
        }

        if ($isOnline) {
            $event['isOnlineMeeting'] = true;
            $event['onlineMeetingProvider'] = 'teamsForBusiness';
        }

        try {
            $msg = $this->graph()
                ->createRequest("post", '/me/events')
                ->addHeader('Content-Type', 'application/json')
                ->attachBody(json_encode($event))
                ->execute();

            return $msg->getBody();
        } catch (ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            $errorMsg = $errorBody['error']['message'] ?? $e->getMessage();
            throw new \Exception("Gagal menambah event kalender: " . $errorMsg, 1);
        }
    }

    /**
     * Add sidang event to user's calendar (convenience method).
     *
     * @param string $namaMhs    Student name
     * @param string $judul      Thesis title
     * @param string $tglSidang  Sidang date (Y-m-d)
     * @param string $waktuMulai Start time (H:i)
     * @param string $waktuSelesai End time (H:i)
     * @param string $ruangan    Room/location
     * @param string $tahapan    Tahapan label (e.g. "Sidang Proposal")
     * @param array  $attendees  Email list (pembimbing, penguji)
     * @return array             Created event data
     */
    public function addSidangEvent(
        string $namaMhs,
        string $judul,
        string $tglSidang,
        string $waktuMulai,
        string $waktuSelesai,
        string $ruangan = '',
        string $tahapan = 'Sidang',
        array $attendees = []
    ): array {
        $subject = " {$tahapan}: {$namaMhs}";
        $start = "{$tglSidang}T{$waktuMulai}:00";
        $end = "{$tglSidang}T{$waktuSelesai}:00";
        $body = "<b>Mahasiswa:</b> {$namaMhs}<br><b>Judul:</b> {$judul}<br><b>Tahapan:</b> {$tahapan}";

        return $this->addEvent($subject, $start, $end, $body, $ruangan, $attendees, false);
    }
}
