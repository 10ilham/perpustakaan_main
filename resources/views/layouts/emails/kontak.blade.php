<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>{{$details['subject']}}</title>
</head>
<body>
    <p>Pesan Baru dari <b>{{$details['nama']}}</b> </p>
    <table>
      <tr>
        <td>Nama</td>
        <td>:</td>
        <td>{{$details['nama']}}</td>
      </tr>
      <tr>
        <td>Email</td>
        <td>:</td>
        <td>{{$details['email']}}</td>
      </tr>
      <tr>
        <td>Telepon</td>
        <td>:</td>
        <td>{{$details['telepon']}}</td>
      </tr>
      <tr>
        <td>Komentar</td>
        <td>:</td>
        <td>{{$details['komentar']}}</td>
      </tr>
    </table>
</body>
</html>
