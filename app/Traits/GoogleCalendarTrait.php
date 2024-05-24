<?php

namespace App\Traits;
use Illuminate\Support\Facades\Auth;
use Google\Client as Google_Client;
use Google\Service\Calendar as GoogleCalendarService;


trait GoogleCalendarTrait
{
    protected function createCalendar($calendarName = 'Ecasify Calendar')
    {
        
        $client = new Google_Client();
        $client->setAuthConfig(config_path('google-service-credentials.json'));
        $client->setScopes([GoogleCalendarService::CALENDAR]);

        $googleCalendarService = new GoogleCalendarService($client);

        $calendar = new \Google_Service_Calendar_Calendar();
        $calendar->setSummary($calendarName); // Set the name of the new calendar
        
        try {
            $createdCalendar = $googleCalendarService->calendars->insert($calendar);
            
            $calendarId = $createdCalendar->getId();
            
            return $calendarId;

        } catch (\Google_Service_Exception $e) {
            return "Error creating calendar: " . $e->getMessage();
        }
    }
    protected function shareCalendar($targetMail,$calendarId = '')
    {
      
        try {
            // Path to your JSON credentials file
            $credentialsPath = config_path('google-service-credentials.json');

            // Create a new Google Client and set up the service account configuration
            $client = new Google_Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(GoogleCalendarService::CALENDAR);

            // Create a new Google Calendar service using the authenticated client
            $googleCalendarService = new GoogleCalendarService($client);

            // Define the calendar ID
            if(!$calendarId){
                $calendarId =  Auth::user()->google_calendar_id;
            }

            // Create a new ACL rule for sharing the calendar
            $rule = new \Google_Service_Calendar_AclRule([
                'scope' => [
                    'type' => 'user',
                    'value' => $targetMail, // The target Gmail account
                ],
                'role' => 'writer', // Adjust the desired access role (writer, reader, etc.)
            ]);

            // Insert the access rule to share the calendar
            $createdRule = $googleCalendarService->acl->insert($calendarId, $rule);

            // return succes message
            return 'Calendar successfully shared with ' . $targetMail;
        } catch (Google_Service_Exception $e) {
            // Handle Google service exceptions, such as API errors
            dd($e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            dd($e->getMessage());
        }
    }

    // You can define more methods related to Google Calendar functionality here
}
