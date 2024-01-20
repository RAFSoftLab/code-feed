<div>
    <table class="table-auto">
        <tr>
            <th>Author</th>
            <th>Message</th>
            <th>Date</th>
            <th>Hash</th>
        </tr>
        @foreach($commits as $commit)
            <tr>
                <td>{{$commit->getAuthor()}}</td>
                <td>{{$commit->getMessage()}}</td>
                <td>{{date('d-m-Y-H-i-s', $commit->getCreatedAt())}}</td>
                <td>{{$commit->getTree()}}</td>
            </tr>
        @endforeach
    </table>

</div>
