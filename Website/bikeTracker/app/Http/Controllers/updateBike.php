<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class updateBike extends Controller
{
    public function update(Request $request)
    {
        $rules = [
           'bikeName' => 'string|max:100',
           'bikeColour' => 'string|max:100',
           'bikeType' => 'string|max:100',
           'bikeMake' => 'string|max:100',
       ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('home')
                ->withErrors($validator)
                ->withInput();
        } else {
            //$user = User::find($id);

            DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)
                ->update(array('bikeName' => $request->input('bikeName')));
            DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)
                 ->update(array('bikeColour' => $request->input('bikeColour')));
            DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)
                  ->update(array('bikeType' => $request->input('bikeType')));
            DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)
                   ->update(array('bikeMake' => $request->input('bikeMake')));

            Session::flash('success', 'Your profile was updated.');

            return Redirect::to('home');
        }
    }
}
