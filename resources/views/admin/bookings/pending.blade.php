<h1>Booking Pending</h1>

@if(session('success'))

    <p style="color:green;">
        {{ session('success') }}
    </p>

@endif

@if(session('error'))

    <p style="color:red;">
        {{ session('error') }}
    </p>

@endif

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Jam</th>
        <th>Jenis</th>
        <th>Status</th>
        <th>Surat</th>
        <th>Aksi</th>
    </tr>

    @foreach($bookings as $booking)

    <tr>

        <td>{{ $booking->booking_id }}</td>

        <td>{{ $booking->tanggal }}</td>

        <td>
            {{ $booking->jam_mulai }}
            -
            {{ $booking->jam_selesai }}
        </td>

        <td>{{ $booking->jenis }}</td>

        <td>{{ $booking->status }}</td>

        <td>
            <a href="{{ asset('storage/' . $booking->surat) }}"
            target="_blank">

            Lihat Surat

            </a>
        </td>

        <td>

            <a href="/admin/bookings/{{ $booking->booking_id }}/approve">

                Approve

            </a>

            |

            <a href="/admin/bookings/{{ $booking->booking_id }}/reject">

                Reject

            </a>

        </td>

    </tr>

    @endforeach

</table>