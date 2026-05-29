<!DOCTYPE html>
<html>
<head>
    <title>All Bookings</title>
</head>
<body>

<h1>Semua Booking</h1>

<table border="1" cellpadding="10">

    <tr>

        <th>ID</th>

        <th>User</th>

        <th>Tanggal</th>

        <th>Jam</th>

        <th>Jenis</th>

        <th>Status</th>

        <th>Ruangan</th>

        <th>Surat</th>

    </tr>

    @foreach($bookings as $booking)

    <tr>

        <td>
            {{ $booking->booking_id }}
        </td>

        <td>
            {{ $booking->user->nama }}
        </td>

        <td>
            {{ $booking->tanggal }}
        </td>

        <td>

            {{ $booking->jam_mulai }}

            -

            {{ $booking->jam_selesai }}

        </td>

        <td>
            {{ $booking->jenis }}
        </td>

        <td>
            {{ $booking->status }}
        </td>

        <td>

            @foreach($booking->rooms as $room)

                {{ $room->nama_ruangan }}

                <br>

            @endforeach

        </td>

        <td>

            @if($booking->surat)

                <a href="{{ asset('storage/' . $booking->surat) }}"
                target="_blank">

                    Lihat Surat

                </a>

            @else

                -

            @endif

        </td>

    </tr>

    @endforeach

</table>

</body>
</html>