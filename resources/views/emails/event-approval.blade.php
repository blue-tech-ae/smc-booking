<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Approval Needed</title>
</head>
<body>
    <p>A new event has been created and requires your approval.</p>
    <ul>
        <li><strong>Title:</strong> {{ $event->title }}</li>
        <li><strong>Location ID:</strong> {{ $event->location_id }}</li>
        <li><strong>Organizer:</strong> {{ $event->organizer_name }}</li>
        <li><strong>Organizer Email:</strong> {{ $event->organizer_email }}</li>
        <li><strong>Organizer Phone:</strong> {{ $event->organizer_phone }}</li>
        <li><strong>Expected Attendance:</strong> {{ $event->expected_attendance }}</li>
        <li><strong>Start:</strong> {{ $event->start_time }}</li>
        <li><strong>End:</strong> {{ $event->end_time }}</li>
        <li><strong>Details:</strong> {{ $event->details }}</li>
    </ul>
    <p>
        <a href="{{ $approveUrl }}" style="display:inline-block;padding:10px 20px;background-color:#1d4ed8;color:#fff;text-decoration:none;">Approve Event</a>
    </p>
</body>
</html>
