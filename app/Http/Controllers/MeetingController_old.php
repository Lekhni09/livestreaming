<?php

namespace App\Http\Controllers;

use App\Models\MeetingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserMeeting;
use Illuminate\Support\Facades\Session;

class MeetingController extends Controller {

    public function meetingUser() {

        return view( 'createMeeting' );
    }

    public function createMeeting() {
        $meeting = Auth::User()->getUserMeetingInfo()->first();

        if ( !isset( $meeting->id ) ) {

            $name = 'agora' . rand( 1111, 9999 );
            $meetingData = createAgoraProject( $name );
            if ( isset( $meetingData->project->id ) ) {
                $meeting = new UserMeeting();
                $meeting->user_id = Auth::User()->id;
                $meeting->app_id = $meetingData->project->vendor_key;
                $meeting->appCertificate = $meetingData->project->sign_key;
                $meeting->channel = $meetingData->project->name;
                $meeting->uid = rand( 11111, 99999 );
                //  dd( $meeting );
                $meeting->save();
            } else {
                echo 'Project not created';
            }
        }

        $meeting  = Auth::User()->getUserMeetingInfo()->first();
        $token    = createToken( $meeting->app_id, $meeting->appCertificate, $meeting->channel );

        $meeting->token = $token;

        $meeting->url = generateRandomString();
        $meeting->save();

        if ( Auth::User()->id == $meeting->user_id ) {
            Session::put( 'meeting', $meeting->url );
        }
        return redirect( 'joinMeeting/' . $meeting->url );
    }

    public function joinMeeting( $url = '' )
    {

        $meeting = UserMeeting::where( 'url', $url )->first();

        if ( isset( $meeting->id ) ) {

            $meeting->app_id = trim( $meeting->app_id );
            $meeting->appCertificate = trim( $meeting->appCertificate );
            $meeting->channel = trim( $meeting->channel );
            $meeting->token = trim( $meeting->token );

            if ( Auth::user()->id == $meeting->user_id ) {
                //meeting create

            } else {
                if ( !Auth::User() ) {
                    $random_user = rand( 111111, 999999 );
                    $this->createEntry( $meeting->user_id, $random_user, $meeting->$url );
                } else {
                    $this->createEntry( $meeting->user_id, Auth::User()->id, $meeting->$url );
                }
            }
            //  prx( get_defined_vars() );
            return view( 'joinUser', get_defined_vars() );
        } else {
            //meeting not exist
            alert( 'Meeting not exist' );
            // return redirect( 'home' );
        }
    }

    public function createEntry( $user_id, $random_user, $url ) {
        $entry = new MeetingEntry();
        $entry->user_id = $user_id;
        $entry->random_user = $random_user;
        $entry->url = $url;
        $entry->status = 1;
        $entry->save();
    }
}
