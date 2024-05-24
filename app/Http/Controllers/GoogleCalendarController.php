<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTimeZone;
use Google\Service\Calendar;
use Google_Service_Calendar;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_CreateConferenceRequest;
use Google_Service_Calendar_Event;
//use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PragmaRX\Countries\Package\Countries;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;
use Goutte\Client;
use Google\Client as Google_Client;
use Google\Service\Calendar as GoogleCalendarService;
use Google\Service\Calendar\ConferenceRequest;
use Google\Service\Calendar\Event;
use App\Models\CalendarEvent;
use Google_Service_Calendar_EventDateTime;
use SimpleXMLElement;
use Google_Service_Calendar_Calendar;


// $userDetail = \App\Models\UserDetail::getUserDetail(Auth::User()->id);
// $timeZone = \App\Models\UserDetail::getTimeZone($userDetail->timezone);


class GoogleCalendarController extends Controller
{


    public function createEvent(Request $request)
    {
        // dd($request->all());
        if(Auth::user()->can('calendar create case')){
        // timeZone
        $userDetail = \App\Models\UserDetail::getUserDetail(Auth::User()->id);
        $timeZone = \App\Models\UserDetail::getTimeZone($userDetail->timezone);
        // dd($timeZone->timezone);

        $request->session()->put('calendar-data', $request->input());
        $client = new Google_Client();
        $client->setAuthConfig(config_path('google-service-credentials.json'));
        $client->setScopes([Google_Service_Calendar::CALENDAR_EVENTS]);
        $service = new Google_Service_Calendar($client);


        $sessionData = $request->session()->get('calendar-data')['eventData'];

        $datetimeStart = Carbon::parse($sessionData['start'] ?? now())->format('Y-m-d\TH:i:s');
// dd($timeZone?->timezone);
        $datetimeEnd = Carbon::parse($sessionData['end'] ?? now())->format('Y-m-d\TH:i:s');
       
        $event = new Google_Service_Calendar_Event([
            //    title
            'summary' => $sessionData['title'] ?? 'Event Title',
            // start time
            'start' => [
                'dateTime' => $datetimeStart,
                'timeZone' =>  $timeZone?->timezone ?? 'America/New_York',
            ],
            // end time
            'end' => [
                'dateTime' => $datetimeEnd,
                'timeZone' => $timeZone?->timezone ?? 'America/New_York',
            ],
            // location
            'location' => $sessionData['location'] ?? '',
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => 1,
                ],
            ],
        ]);
        // dd($event);
        // Insert event
        $createdEvent = $service->events->insert(Auth::user()->google_calendar_id, $event, ['conferenceDataVersion' => 1]);
        //  dd($createdEvent);
        return response()->json(['message' => 'Event created successfully']);
        }else{
            return response()->json(['error' => 'Permission Denied.']);
        }
    }


    public function getEvents($id)
    {
        if(Auth::user()->can('calendar case')){
        Session::put('caseId', $id);
        try {
            // Create a Google Client instance
            $client = new Google_Client();
            $client->setDeveloperKey("AIzaSyDOAW83RrD3hu9QhPwRGRmy2NU2Vwsd-CI"); // developer key
            $client->setScopes([Google_Service_Calendar::CALENDAR_READONLY]);
            $client->setAuthConfig(config_path('google-service-credentials.json')); // Path to your credentials file

            // Create a Google Calendar service
            $service = new Google_Service_Calendar($client);

            // Retrieve events from the specified calendar
            $calendarId =  Auth::user()->google_calendar_id;

            $events = $service->events->listEvents($calendarId);

            // Process and get the event items
            $eventList = $events->getItems();

            // Return events as JSON response
            return response()->json($eventList);
        } catch (\Exception $e) {
            // Handle exceptions, such as API errors or invalid calendar IDs
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }else{
        return response()->json(['error' => 'Permission Denied.'],403);
    }
    }

    public function updateEvent(Request $request)
    {
        if(Auth::user()->can('calendar edit case')){

        try {
            // timeZone
            $userDetail = \App\Models\UserDetail::getUserDetail(Auth::User()->id);
             $timeZone = \App\Models\UserDetail::getTimeZone($userDetail?->timezone);
            // Get the event ID from the request
            $eventId = $request->input('id');

            // Path to your JSON credentials file
            $credentialsPath = config_path('google-service-credentials.json');

            // Create a new Google client and set up the service account configuration
            $client = new Google_Client();
            $client->setDeveloperKey('AIzaSyDOAW83RrD3hu9QhPwRGRmy2NU2Vwsd-CI'); //  developer key
            $client->addScope([Google_Service_Calendar::CALENDAR_EVENTS]);
            $client->setAuthConfig($credentialsPath);

            // Create a new Google Calendar service using the authenticated client
            $service = new Google_Service_Calendar($client);

            // Get the existing event
            $calendarId =  Auth::user()->google_calendar_id;
            $event = $service->events->get($calendarId, $eventId);

            // Parse and format start and end dates
            $datetimeStart = Carbon::parse($request->input('start') ?? now())->format('Y-m-d\TH:i:s');
            $datetimeEnd = Carbon::parse($request->input('end') ?? now()->addHours(2))->format('Y-m-d\TH:i:s');

            // dd($datetimeStart,$datetimeEnd);
            // Update event details
            if ($request->input('title')) {
                $event->setSummary($request->input('title'));
            }
            if ($request->input('location')) {
                $event->setLocation($request->input('location'));
            }
            if ($request->input('start')) {
                // $datetimeStart = new Carbon($request->input('start'));
                // $formattedStart = $datetimeStart->format('Y-m-d\TH:i:s');
                // Set the start date and time with time zone
                $event->setStart(new Google_Service_Calendar_EventDateTime([
                    'dateTime' => $datetimeStart,
                    'timeZone' => $timeZone?->timezone ?? 'America/New_York',
                ]));
            }
            if ($request->input('end')) {
                // $datetimeEnd = new Carbon($request->input('end'));
                // $formattedEnd = $datetimeEnd->format('Y-m-d\TH:i:s');

                // Set the end date and time with time zone
                $event->setEnd(new Google_Service_Calendar_EventDateTime([
                    'dateTime' => $datetimeEnd,
                    'timeZone' => $timeZone?->timezone ?? 'America/New_York',
                ]));
            }

            // Update the event
            $updatedEvent = $service->events->update($calendarId, $event->getId(), $event);

            // Return a success response
            return response()->json(['message' => 'Event Updated Successfully']);
        } catch (\Exception $e) {
            // Handle exceptions, such as API errors or invalid event IDs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }else{
        return response()->json(['error' => 'Permission Denied.']);
    }
    }

    // deleteEvent
    public function deleteEvent(Request $request)
    {
        if(Auth::user()->can('calendar delete case')){

        try {
            // Get the event ID from the request
            $eventId = $request->input('id');

            // Path to your JSON credentials file
            $credentialsPath = config_path('google-service-credentials.json');

            // Create a new Google client and set up the service account configuration
            $client = new Google_Client();
            $client->setDeveloperKey('AIzaSyDOAW83RrD3hu9QhPwRGRmy2NU2Vwsd-CI'); //  developer key
            $client->setAuthConfig($credentialsPath);
            $client->addScope([Google_Service_Calendar::CALENDAR_EVENTS]);

            // Set offline access type and include granted scopes
            $client->setAccessType('offline');
            $client->setIncludeGrantedScopes(true);

            // Create a new Google Calendar service using the authenticated client
            $service = new Google_Service_Calendar($client);

            // Delete the specified event
            $calendarId =  Auth::user()->google_calendar_id;
            $service->events->delete($calendarId, $eventId);

            // Return a success response
            return response()->json(['message' => 'Event Deleted Successfully']);
        } catch (\Exception $e) {
            // Handle exceptions, such as API errors or invalid event IDs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }else{
        return response()->json(['error' => 'Permission Denied.']);
    
    }
    }
}
