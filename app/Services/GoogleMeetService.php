<?php
namespace App\Services;
use Google\Client;
use Google\Service\Calendar;
use App\Models\ClassDetail;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Coordinator;
use App\Tools\Repositories\Crud;
use Google\Service\Calendar\Event;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\GoogleOAuthService;
use Auth;

class GoogleMeetService
{
    private $client;
    private $calendar;
    protected $classDetailModel;
    protected $tutorValid;
    protected $isTutorToken;
    protected $isTutorRefreshToken;

    // public function __construct(ClassDetail $class_detail)
    // {
    //     $this->classDetailModel = new Crud($class_detail);
    //     $this->client = new Client();
    //     $this->client->setAuthConfig(storage_path('app/google/credientials.json'));
    //     // $this->client->setAuthConfig(storage_path('app/google/service_key_account.json'));
    //     $this->client->addScope(Calendar::CALENDAR);
    //     $this->client->setApplicationName("CEF Online");
     
    //     // $this->client->setSubject('user@your-domain.com'); // Optional: Impersonate a user in your domain

    //     //////////// ACCESS TOKEN LOGIC /////////////
    //     // try {
    //     //     $accessToken = $this->client->fetchAccessTokenWithAssertion();
    //     //     // $accessTokenCurrent = $this->client->getAccessToken(); //array
    //     //     // $bearerToken =  $accessToken['id_token'];
    //     //     $this->client->setAccessToken($accessToken);
    //     // } catch (Exception $e) {
    //     //     \Log::error('Error: ' . $e->getMessage());
    //     //     throw $e;
    //     //     // echo 'Error: ' . $e->getMessage();
    //     // }

    //     $this->calendar = new Calendar($this->client);
    // }


    public function __construct(GoogleOAuthService $oauthService,ClassDetail $class_detail, $instructor_user_id = null)
    {
       
       
        $tutor = User::find($instructor_user_id);
   
        $this->classDetailModel = new Crud($class_detail);
        $this->client = $oauthService->getClient();
        $response =$oauthService->refreshTokenIfNeeded($tutor);
        $this->tutorValid = ($response !== 'null_tutor');
        $this->isTutorToken = ($response !== 'no_access_token');
        $this->isTutorRefreshToken = ($response !== 'no_refresh_access_token');
      
        if ($this->tutorValid && $this->isTutorToken && $this->isTutorRefreshToken) {
            $this->calendar = new Calendar($this->client);
        }
    
        // dd($this->tutorValid);
        // $this->calendar = new Calendar($this->client);
    
      
    }

    // Method to check if the tutor is valid
    public function isTutorValid()
    {
        return $this->tutorValid;
    }

      // Method to check if the tutor is valid
      public function isTutorToken()
      {
          return $this->isTutorToken;
      }


      public function isTutorRefreshToken()
      {
          return $this->isTutorRefreshToken;
      }




    public function createClass(array $data , $id = null, $group_id = null)
    {

        // Get user emails
        $instructor = User::findOrFail($data['instructor_user_id_meet']);
        // $coordinator = User::findOrFail($data['coordinator_user_id_meet']);
        $coordinators = User::whereIn('id',$data['coordinator_user_ids_meet'])->get();
        $students = User::whereIn('id', $data['student_user_ids_meet'])->get();

        $attendees = array();
      
        // Add students
        foreach ($students as $student) {
            $attendees[] = [
                'email' => $student->email,
                // 'responseStatus' => 'needsAction'
                'responseStatus' => 'accepted'
            ];
        }

          // Add coordinators if available
        if(!$coordinators->isEmpty()){
            foreach ($coordinators as $coordinator) {
                $attendees[] = [
                    'email' => $coordinator->email,
                    // 'responseStatus' => 'needsAction',
                    'responseStatus' => 'accepted',
                    'additionalPrivileges' => ['MODERATE']
                ];
            }
        }
      
        $attendees[] = [
            'email' => $instructor->email,
            'self' => true
        ];

        if($id != null && $group_id != null){
            // $existingEvent = ClassDetail::where('class_id', $data['class_id'])
            // ->where('class_date', $data['class_date'])
            // ->first();
            $existingEvent = ClassDetail::where('group_id',$group_id)->where('class_date',$data['class_date'])
               ->whereNotNull('google_event_id')->first();
            if ($existingEvent) {
                // Reuse the existing event's Google Meet link
                $class_detail_data = [
                    'class_id' => $data['class_id'],
                    'student_id' => $data['student_id'],
                    'instructor_id' => $data['instructor_id'],
                    'course_id' => $data['course_id'],
                    'class_date' => $data['class_date'],
                    'class_time' => $data['class_time'],
                    'status' => $data['class_status'],
                    'start_time_unix' => $data['start_time_unix'],
                    'end_time_unix' => $data['end_time_unix'],
                    'start_time' => $data['start_time_2'],
                    'end_time' => $data['end_time_2'],
                    'google_event_id' => $existingEvent->google_event_id,
                    'google_meet_link' => $existingEvent->google_meet_link,
                ];
                $returnData = $this->classDetailModel->update($class_detail_data,$id);
                return $returnData;
            }
            else{
                $event = new Event(array(
                    'summary' => $data['title_meet'],
                    'location' => 'Google Meet',
                    'description' => $data['description_meet'],
                    'start' => array(
                        'dateTime' => Carbon::parse($data['start_time_meet'])->toRfc3339String(),
                        'timeZone' => config('app.timezone'),
                    ),
                    'end' => array(
                        'dateTime' => Carbon::parse($data['end_time_meet'])->toRfc3339String(),
                        'timeZone' => config('app.timezone'),
                    ),
                    'conferenceData'=> [
                        'createRequest'=> [
                           'requestId' => Str::uuid(),
                            'conferenceSolutionKey'=> [
                              'type'=> "hangoutsMeet"
                            ],
                        ]
                        ],
              
                    // 'attendees' => $attendees,
                    // 'organizer' => 
                    // array(
                    //     'email' => $instructor->email,
                    //     'self' => true
                    // ),
                    // 'guestsCanModify' => false,
                    // 'guestsCanInviteOthers' => true
                ));

                try {
                    $createdEvent = $this->calendar->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
                    $createdEvent->setAttendees(
                        $attendees
                    );
                    $class_detail_data = [
                        'class_id' => $data['class_id'],
                        'student_id' => $data['student_id'],
                        'instructor_id' => $data['instructor_id'],
                        'course_id' => $data['course_id'],
                        // 'class_date' => $data['lesson_date_updated'],
                        'class_date' => $data['class_date'],
                        'class_time' => $data['class_time'],
                        'status' => $data['class_status'],
                        'start_time_unix' => $data['start_time_unix'],
                        'end_time_unix' => $data['end_time_unix'],
                        'start_time' => $data['start_time_2'],
                        'end_time' => $data['end_time_2'],
                        'google_event_id' => $createdEvent->id,
                        'google_meet_link' => $createdEvent->hangoutLink,
                    ];
                    $returnData = $this->classDetailModel->update($class_detail_data,$id);
                    return $returnData;
                } 
                catch (\Exception $e) {
                    \Log::error('Google Meet Class Creation Error: ' . $e->getMessage());
                    throw $e;
                }
            }
        }
        elseif($id != null && $group_id == null){
            $existingEvent = ClassDetail::where('id',$id)->where('class_date',$data['class_date'])
               ->whereNotNull('google_event_id')->first();
            if ($existingEvent) {
                // Reuse the existing event's Google Meet link
                $class_detail_data = [
                    'class_id' => $data['class_id'],
                    'student_id' => $data['student_id'],
                    'instructor_id' => $data['instructor_id'],
                    'course_id' => $data['course_id'],
                    'class_date' => $data['class_date'],
                    'class_time' => $data['class_time'],
                    'status' => $data['class_status'],
                    'start_time_unix' => $data['start_time_unix'],
                    'end_time_unix' => $data['end_time_unix'],
                    'start_time' => $data['start_time_2'],
                    'end_time' => $data['end_time_2'],
                    'google_event_id' => $existingEvent->google_event_id,
                    'google_meet_link' => $existingEvent->google_meet_link,
                ];
                $returnData = $this->classDetailModel->update($class_detail_data,$id);
                return $returnData;
            }
            else{
                $event = new Event(array(
                    'summary' => $data['title_meet'],
                    'location' => 'Google Meet',
                    'description' => $data['description_meet'],
                    'start' => array(
                        'dateTime' => Carbon::parse($data['start_time_meet'])->toRfc3339String(),
                        'timeZone' => config('app.timezone'),
                    ),
                    'end' => array(
                        'dateTime' => Carbon::parse($data['end_time_meet'])->toRfc3339String(),
                        'timeZone' => config('app.timezone'),
                    ),
                    'conferenceData'=> [
                        'createRequest'=> [
                           'requestId' => Str::uuid(),
                            'conferenceSolutionKey'=> [
                              'type'=> "hangoutsMeet"
                            ],
                        ]
                        ],
                ));

                try {
                    $createdEvent = $this->calendar->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
                    $createdEvent->setAttendees(
                        $attendees
                    );
                    $class_detail_data = [
                        'class_id' => $data['class_id'],
                        'student_id' => $data['student_id'],
                        'instructor_id' => $data['instructor_id'],
                        'course_id' => $data['course_id'],
                        'class_date' => $data['class_date'],
                        'class_time' => $data['class_time'],
                        'status' => $data['class_status'],
                        'start_time_unix' => $data['start_time_unix'],
                        'end_time_unix' => $data['end_time_unix'],
                        'start_time' => $data['start_time_2'],
                        'end_time' => $data['end_time_2'],
                        'google_event_id' => $createdEvent->id,
                        'google_meet_link' => $createdEvent->hangoutLink,
                    ];
                    $returnData = $this->classDetailModel->update($class_detail_data,$id);
                    return $returnData;
                } 
                catch (\Exception $e) {
                    \Log::error('Google Meet Class Creation Error: ' . $e->getMessage());
                    throw $e;
                }
            }
        } 
        else{
            $event = new Event(array(
                'summary' => $data['title_meet'],
                'location' => 'Google Meet',
                'description' => $data['description_meet'],
                'start' => array(
                    'dateTime' => Carbon::parse($data['start_time_meet'])->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ),
                'end' => array(
                    'dateTime' => Carbon::parse($data['end_time_meet'])->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ),
                'conferenceData'=> [
                    'createRequest'=> [
                       'requestId' => Str::uuid(),
                        'conferenceSolutionKey'=> [
                          'type'=> "hangoutsMeet"
                        ],
                    ]
                    ],
        
                'guestsCanModify' => false,
                'guestsCanInviteOthers' => true
            ));
         
            try {
                $createdEvent = $this->calendar->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
           
                // $createdEvent->setAttendees(
                //     array(
                //         array('email' => 'attendee@example.com'),
                //     )
                // );
                $createdEvent->setAttendees(
                    $attendees
                );
           
                $class_detail_data = [
                    'class_id' => $data['class_id'],
                    'student_id' => $data['student_id'],
                    'instructor_id' => $data['instructor_id'],
                    'course_id' => $data['course_id'],
                    // 'class_date' => $data['lesson_date_updated'],
                    'class_date' => $data['class_date'],
                    'class_time' => $data['class_time'],
                    'status' => $data['class_status'],
                    'start_time_unix' => $data['start_time_unix'],
                    'end_time_unix' => $data['end_time_unix'],
                    'start_time' => $data['start_time_2'],
                    'end_time' => $data['end_time_2'],
                    'google_event_id' => $createdEvent->id,
                    'google_meet_link' => $createdEvent->hangoutLink,
                ];
    
                $returnData=$this->classDetailModel->create($class_detail_data);
                return $returnData;
            } catch (\Exception $e) {
                \Log::error('Google Meet Class Creation Error: ' . $e->getMessage());
                throw $e;
            }
        }

      
        // if ($existingEvent) {
        //     // Reuse the existing event's Google Meet link
        //     $class_detail_data = [
        //         'class_id' => $data['class_id'],
        //         'student_id' => $data['student_id'],
        //         'instructor_id' => $data['instructor_id'],
        //         'course_id' => $data['course_id'],
        //         'class_date' => $data['class_date'],
        //         'class_time' => $data['class_time'],
        //         'status' => $data['class_status'],
        //         'start_time_unix' => $data['start_time_unix'],
        //         'end_time_unix' => $data['end_time_unix'],
        //         'start_time' => $data['start_time_2'],
        //         'end_time' => $data['end_time_2'],
        //         'google_event_id' => $existingEvent->google_event_id,
        //         'google_meet_link' => $existingEvent->google_meet_link,
        //     ];
        //     if($id != null){
        //         $returnData = $this->classDetailModel->update($class_detail_data,$id);
        //     }
        //     else{
        //         $getCheck = ClassDetail::where('class_id', $data['class_id'])
        //         ->where('class_date', $data['class_date'])
        //         ->whereNull('google_event_id')
        //         ->first();
        //         if($getCheck){
        //             $returnData = $this->classDetailModel->update($class_detail_data, $getCheck->id);
        //         }
               
        //     }
           
        //     return $returnData;
        // }
        // else{
        // // $event = new Google_Service_Calendar_Event(array(
        //     $event = new Event(array(
        //         'summary' => $data['title_meet'],
        //         'location' => 'Google Meet',
        //         'description' => $data['description_meet'],
        //         'start' => array(
        //             'dateTime' => Carbon::parse($data['start_time_meet'])->toRfc3339String(),
        //             'timeZone' => config('app.timezone'),
        //         ),
        //         'end' => array(
        //             'dateTime' => Carbon::parse($data['end_time_meet'])->toRfc3339String(),
        //             'timeZone' => config('app.timezone'),
        //         ),
        //         'conferenceData'=> [
        //             'createRequest'=> [
        //                'requestId' => Str::uuid(),
        //                 'conferenceSolutionKey'=> [
        //                   'type'=> "hangoutsMeet"
        //                 ],
        //             ]
        //             ],
          
        //         'attendees' => $attendees,
        //         'organizer' => 
        //         array(
        //             'email' => $instructor->email,
        //             'self' => true
        //         ),
        //         'guestsCanModify' => false,
        //         'guestsCanInviteOthers' => false
        //     ));
         
        //     try {
        //         $createdEvent = $this->calendar->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
          
        //         $class_detail_data = [
        //             'class_id' => $data['class_id'],
        //             'student_id' => $data['student_id'],
        //             'instructor_id' => $data['instructor_id'],
        //             'course_id' => $data['course_id'],
        //             // 'class_date' => $data['lesson_date_updated'],
        //             'class_date' => $data['class_date'],
        //             'class_time' => $data['class_time'],
        //             'status' => $data['class_status'],
        //             'start_time_unix' => $data['start_time_unix'],
        //             'end_time_unix' => $data['end_time_unix'],
        //             'start_time' => $data['start_time_2'],
        //             'end_time' => $data['end_time_2'],
        //             'google_event_id' => $createdEvent->id,
        //             'google_meet_link' => $createdEvent->hangoutLink,
        //         ];
    
        //         $returnData=$this->classDetailModel->create($class_detail_data);
        //         return $returnData;
        //     } catch (\Exception $e) {
        //         \Log::error('Google Meet Class Creation Error: ' . $e->getMessage());
        //         throw $e;
        //     }
        // }    
    }


    public function createClass2(array $data)
    {

        // Get user emails
        $instructor = User::findOrFail($data['instructor_user_id_meet']);
        // $coordinator = User::findOrFail($data['coordinator_user_id_meet']);
        $coordinators = User::whereIn('id',$data['coordinator_user_ids_meet'])->get();
        $students = User::whereIn('id', $data['student_user_ids_meet'])->get();

        $attendees = array();
        // Prepare attendees
        // $attendees = collect([
        //     [
        //         'email' => $instructor->email,
        //         'responseStatus' => 'needsAction',
        //         'organizer' => true
        //     ],
        //     // [
        //     //     'email' => $coordinator->email,
        //     //     'responseStatus' => 'needsAction',
        //     //     'additionalPrivileges' => ['MODERATE']
        //     // ]
        // ]);

        // Add students
        foreach ($students as $student) {
            // $attendees->push([
            //     'email' => $student->email,
            //     'responseStatus' => 'needsAction'
            // ]);
            $attendees[] = [
                'email' => $student->email,
                'responseStatus' => 'needsAction'
            ];
        }

          // Add coordinators if available
        if(!$coordinators->isEmpty()){
            foreach ($coordinators as $coordinator) {
                // $attendees->push([
                //     'email' => $coordinator->email,
                //     'responseStatus' => 'needsAction',
                //     'additionalPrivileges' => ['MODERATE']
                // ]);
                $attendees[] = [
                    'email' => $coordinator->email,
                    'responseStatus' => 'needsAction',
                    'additionalPrivileges' => ['MODERATE']
                ];
            }
        }
      
    
        // $event = new Google_Service_Calendar_Event(array(
        $event = new Event(array(
            'summary' => $data['title_meet'],
            'location' => 'Google Meet',
            'description' => $data['description_meet'],
            'start' => array(
                'dateTime' => Carbon::parse($data['start_time_meet'])->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ),
            'end' => array(
                'dateTime' => Carbon::parse($data['end_time_meet'])->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ),
            'conferenceData'=> [
                'createRequest'=> [
                   'requestId' => Str::uuid(),
                    'conferenceSolutionKey'=> [
                      'type'=> "hangoutsMeet"
                    ],
                ]
                ],
            // 'conferenceData' => array(
            //     'createRequest' => array(
            //         'requestId' => uniqid(),
            //         'conferenceSolutionKey' => array('type' => 'hangoutsMeet')
            //     )
            // ),
            // 'attendees' => $attendees->toArray(),
            'attendees' => $attendees,
            'organizer' => 
            array(
                'email' => $instructor->email,
                'self' => true
            ),
            'guestsCanModify' => false,
            'guestsCanInviteOthers' => false
        ));
     
        try {
            $createdEvent = $this->calendar->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
      
            $class_detail_data = [
                'class_id' => $data['class_id'],
                'student_id' => $data['student_id'],
                'instructor_id' => $data['instructor_id'],
                'course_id' => $data['course_id'],
                // 'class_date' => $data['lesson_date_updated'],
                'class_date' => $data['class_date'],
                'class_time' => $data['class_time'],
                'status' => $data['class_status'],
                'start_time_unix' => $data['start_time_unix'],
                'end_time_unix' => $data['end_time_unix'],
                'start_time' => $data['start_time_2'],
                'end_time' => $data['end_time_2'],
                'google_event_id' => $createdEvent->id,
                'google_meet_link' => $createdEvent->hangoutLink,
            ];

            $returnData=$this->classDetailModel->create($class_detail_data);
            return $returnData;
        } catch (\Exception $e) {
            \Log::error('Google Meet Class Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }
}