<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Bike extends Controller
{


     /**
    * Edit Single Customer for View relation only id of customer
    */
    public function view()
    {
        //find customer
        $bikeOwner = \Auth::user();
        $userBikeTrackerID = \Auth::user()->bikeTrackerID;
        $bike = \DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();
        $bikeLocation = \DB::table('gpslocations')->where('bikeTrackerID', '=', $userBikeTrackerID)->orderBy('updated_at', 'desc')->first();
        if ($bikeLocation == null) { // GPS not synced for this bike
            $bikeLocation = new \stdClass();
            $bikeLocation->lat = " ";
            $bikeLocation->long = " ";
        }
        //show the edit form
        return \View::make('home', ['bike' => $bike], ['bikeLocation' => $bikeLocation]);
    }

    /**
   * Edit Single Customer for View relation only id of customer
   */
    public function timeline()
    {
        if (\Input::get('date')) {
            $showRouteForDate = \Input::get('date');
        } else {
            $showRouteForDate = date('Y-m-d');
        }

        //find customer
        $bikeOwner = \Auth::user();
        $userBikeTrackerID = \Auth::user()->bikeTrackerID;
        $bike = \DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();
        $bikeLocation = \DB::table('gpslocations')->where('bikeTrackerID', '=', $userBikeTrackerID)
         ->whereDate('updated_at', '=', $showRouteForDate)->orderBy('updated_at', 'desc')->get();
        $bikeLocation->isData = true;

        if ($bikeLocation->first() == null) { // GPS not synced for this bike
            $bikeLocation = new \stdClass();
            $bikeLocation->lat = " ";
            $bikeLocation->long = " ";
            $bikeLocation->updated_at = " ";
            $bikeLocation->isData = false;
        }
        //show the edit form
        return \View::make('timeline', ['bike' => $bike], ['bikeLocation' => $bikeLocation]);
    }


    /**
    * Edit Single Customer for View relation only id of customer
    */
    public function edit()
    {
        //find customer
        $bikeOwner = \Auth::user();
        $userBikeTrackerID = \Auth::user()->bikeTrackerID;
        $bike = \DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->first();

        //show the edit form
        return \View::make('editBike')->with('bike', $bike);
    }

    public function update()
    {
        $rules = [
           'bikeName' => 'required|string|max:100',
           'bikeColour' => 'string|max:100',
           'bikeType' => 'string|max:100',
           'bikeMake' => 'string|max:100',
       ];

        $validator = \Validator::make(\Input::all(), $rules);

        if ($validator->fails()) {
            return \Redirect::to('editBike')
                ->withErrors($validator)
                ->withInput();
        } else {
            //$user = User::find($id);

            $userBikeTrackerID = \Auth::user()->bikeTrackerID;
            $bikeName = \Input::get('bikeName');
            $bikeColour = \Input::get('bikeColour');
            $bikeType = \Input::get('bikeType');
            $bikeMake = \Input::get('bikeMake');

            \DB::table('bikes')->where('bikeTrackerID', '=', $userBikeTrackerID)->update(array(
             'bikeName' => $bikeName,
             'bikeColour' => $bikeColour,
             'bikeType' => $bikeType,
             'bikeMake' => $bikeMake,
            ));

            \Session::flash('success', 'Your profile was updated.');

            return \Redirect::to('home');
        }
    }
}
