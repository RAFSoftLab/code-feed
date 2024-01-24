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
                <td>{{$commit->getAuthor()}}</td>
                <td>{{$commit->getMessage()}}</td>
                <td>{{date('d-m-Y-H-i-s', $commit->getCreatedAt())}}</td>
                <td>{{$commit->getTree()}}</td>
            </tr>
        @endforeach
        </x-slot:tbody>
    </x-turbine-ui-table >
</div>
