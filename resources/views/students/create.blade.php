@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">Students Data Entry </div>
    <div class="card-body">
       
        <form action="{{ url('students') }}" method="post">
            {!! csrf_field() !!}
            <label>Name</label><br/>
            <input type="text" name="name" id="name" class="form-control"><br/>
            
            <label>Address</label><br/>
            <input type="text" name="address" id="address" class="form-control"><br/>
            
            <label>Mobile</label><br/>
            <input type="text" name="mobile" id="mobile" class="form-control"><br/>

            <!-- 🌟 NEW ACTIVE COURSE INTEGRATION MATRIX DROPDOWN -->
            <label>Select Enrolled Course *</label><br/>
            <select name="course_id" id="course_id" class="form-control" required style="font-weight: 600;">
                <option value="" disabled selected>-- Choose Course Track --</option>
                @foreach(DB::table('courses')->orderBy('name', 'asc')->get() as $course)
                    <option value="{{ $course->id }}">{{ $course->name }} (Rs. {{ number_format($course->fee, 2) }})</option>
                @endforeach
            </select><br/>

            <!-- 🌟 NEW DYNAMIC FEE PAYMENT INSTALLMENTS PREFERENCE PLAN PICKER -->
            <label>Fee Payment Installments Preference Plan *</label><br/>
            <select name="requested_installments" id="requested_installments" class="form-control" required style="font-weight: 600; color: #1e293b;">
                <option value="1">Full One-Time Fee Payment (No Installments)</option>
                <option value="2">2 Easy Monthly Installments Plan</option>
                <option value="3" selected>3 Easy Monthly Installments Plan (Standard 3-Months Threshold)</option>
                <option value="4">4 Easy Monthly Installments Plan (Extended Scope)</option>
                <option value="6">6 Easy Monthly Installments Plan (Custom Extended Plan)</option>
            </select>
            <small style="color: #64748b; font-size: 11px; display: block; margin-top: 4px; margin-bottom: 20px;">
                The system will automatically divide the full course fees evenly based on this choice, setting the installment due date on the 10th of every consecutive month.
            </small>

            <input type="submit" value="Save" class="btn btn-success"><br/>
        </form>

    </div>
</div>

@stop
