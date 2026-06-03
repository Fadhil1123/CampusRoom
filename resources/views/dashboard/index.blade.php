<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

    <h1>Dashboard Admin</h1>

    <hr>

    <h3>Total User: {{ $totalUser }}</h3>

    <h3>Total Ruangan: {{ $totalRoom }}</h3>

    <h3>Booking Pending: {{ $pendingBooking }}</h3>

    <h3>Booking Approved: {{ $approvedBooking }}</h3>

    <h3>Total Booking: {{ $totalBooking }}</h3>

    <hr>

    <h3>Statistik Jenis Booking</h3>

    <p>
        Total Booking Perkuliahan :
        {{ $totalPerkuliahan }}
    </p>

    <p>
        Total Booking Kegiatan :
        {{ $totalBookingKegiatan }}
    </p>    

</body>
</html>