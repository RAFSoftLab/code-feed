<div>
    <x-turbine-ui-table
        variant="light"
        divide
        striped
    >
        <x-slot:thead>
            <th>Author</th>
            <th>Message</th>
            <th>Date</th>
            <th>Hash</th>
        </x-slot:thead>
        <x-slot:tbody>
        @foreach($commits as $commit)
            <tr class="font-mono">
                <td><a href={{'https://github.com/'.$commit->organization.'/'. $commit->repository.'/commit/'.$commit->tree}}>{{$commit->author}}</a></td>
                <td>{{$commit->message}}</td>
                <td>{{date('d-m-Y-H-i-s', $commit->createdAt)}}</td>
                <td>{{$commit->tree}}</td>
            </tr>
        @endforeach
        </x-slot:tbody>
    </x-turbine-ui-table >
</div>
