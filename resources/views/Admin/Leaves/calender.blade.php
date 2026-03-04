@extends('layout.app')

@section('content')
<div class="content">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title text-dark">
                    <i class="fas fa-calendar-alt text-primary mr-2"></i>
                    Leave Calendar
                </h3>
                <p class="text-muted mb-0">View all leaves and holidays</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>

<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
}
.fc-event {
    cursor: pointer;
}
</style>

<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        events: @json($events),
        eventClick: function(event) {
            if(event.url) {
                window.location.href = event.url;
                return false;
            }
        },
        dayClick: function(date, jsEvent, view) {
            // Optional: Add leave on day click
            console.log('Clicked on: ' + date.format());
        },
        eventRender: function(event, element) {
            element.tooltip({
                title: event.title,
                placement: 'top',
                trigger: 'hover'
            });
        }
    });
});
</script>
@endsection