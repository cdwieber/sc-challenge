<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Chris' Sportrecruits Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    </head>
    <body>
        <div class="container">
            @foreach($teams as $team => $players)
                <h1>{{ $team }}</h1>
                <p>Players: <strong>{!! count($players) !!}</strong></p>
                <p>Average Ranking: <strong>{!! array_sum(array_column($players, 'ranking')) / count($players) !!}</strong></p>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Ranking</th>
                            <th scope="col">Goalie</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($players as $player)
                        <tr>
                            <td>{{$player['name']}}</td>
                            <td>{{$player['ranking']}}</td>
                            <td>{!!$player['goalie'] == true ? '&#10003;' : '' !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>
