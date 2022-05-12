<table style="width:100%;" border="1">
    <tr>
        <th colspan="11"><center>ACADEMIC PERFOMANCE</center></th>
    </tr>
    <tr>
        <th> Subjects </th>
        <th> First <br> Test </th>
        <th> Second <br> Test </th>
        <th> CA <br> Total </th>
        <th> Exam </th>
        <th> Total </th>
        <th> Class <br> Highest <br>Score </th>
        <th> Class <br> Lowest <br> Score </th>
        <th> Position </th>
        <th> Grade </th>
        <th> Remark </th>
    </tr>
    <tr>
        <th>Max. Obtainable Mark</th>
        <th>20%</th>
        <th>20%</th>
        <th>40%</th>
        <th>60%</th>
        <th>100%</th>
        <th>100%</th>
        <th>100%</th>
        <th></th>
        <th></th>
        <th></th>
    </tr>

     @foreach( $data['result'] as $result )
        <tr>
            <td> @php echo getSubjectName($school,$result->subjectId) @endphp</td>
            <td>@php echo $result->firsttest @endphp</td>
            <td>@php echo $result->secondtest @endphp</td>
            <td>@php echo $result->catotal @endphp</td>
            <td>@php echo $result->exam @endphp</td>
            <td>@php echo $result->total @endphp</td>
            <td>@php echo $result->high @endphp</td>
            <td>@php echo $result->low @endphp</td>
            <td>@php echo $result->pos @endphp</td>
            <td>@php echo $result->grade @endphp</td>
            <td>@php echo $result->remark @endphp</td>
        </tr>
    @endforeach
</table>
