@php
 $app_url = getConfigValue($school, 'app_url');
@endphp
<div class="result-wrapper">
    <div id="watermark">
        <img src="@php echo $app_url @endphp/template/assets/img/watermark.png" height="100%" width="100%" />
    </div>
    <div class="results">
      <table style="width:100%;text-align:center">
        <tr>
          <td>
            <div class="school_logo">
              @php
                $school_logo = getConfigValue($school, 'school_logo');
              @endphp
              <img src="@php echo $school_logo @endphp" alt="@php echo getConfigValue($school, 'school_name') @endphp"/>
            </div>
          </td>
          <td>
            <div class="school_name">
              <strong style="font-size:0.5cm;text-transform:uppercase;">{{ getConfigValue($school, 'school_name'); }}</strong>
            </div>
              Moto: {{ getConfigValue($school, 'school_moto') }} <br/>Address: {{ $data['campus']->address }} <br/>
              Telephone: {{ getConfigValue($school,"school_tel1") .' '. getConfigValue($school, "school_tel2") }} <br/>
              <b>Continuous Assessment Report {{ getConfigValue($school, "year")  }} </b>

          </td>
          <td>
            <div class="student_image">
              @if($data['student']->avatar != '')
                <img src="@php echo $app_url . '/uploads'. $data['student']->avatar @endphp" alt="@php echo $data['student']->first_name .' '.$data['student']->last_name @endphp"/>
              @else
                <img src="http://grad.fkkas.com/placeholder/avatar.png" alt="@php echo $data['student']->first_name .' '.$data['student']->last_name @endphp"/>
              @endif

            </div>
          </td>
        </tr>

      </table>

      <table style="width:100%;" border="1">
        <tr>
          <th colspan="2"> <center>STUDENT'S PERSONAL DATA</center></th>
          <th colspan="3"> <center>ATTENDANCE </center></th>
        </tr>
        <tr>
          <td>NAME</td>
          <td> @php echo strtoupper( $data['student']->first_name . ' '. $data['student']->last_name .' '.$data['student']->middle_name ) @endphp</td>
          <td>Times Sch. Opened</td>
          <td>Times Present</td>
          <td>Times Absent</td>
        </tr>
        <tr>
          <td>ADMISSION NO:</td>
          <td> @php echo strtoupper($data['student']->reg_no) @endphp</td>
          <td>@php echo ( $data['attendance']['total'] == "0" ? 'N/A' : $data['attendance']['total'] ) @endphp</td>
          <td>@php echo ( $data['attendance']['total'] == "0" ? 'N/A' : $data['attendance']['present'] ) @endphp</td>
          <td>@php echo ( $data['attendance']['total'] == "0" ? 'N/A' : $data['attendance']['absent'] ) @endphp</td>
        </tr>
        <tr>
          <td>SEX:</td>
          <td>@php echo strtoupper($data['student']->gender)@endphp</td>
          <th>FEE</th>
          <th colspan="2"> TERMINAL DURATION </th>
        </tr>
        <tr>
          <td>CLASS:</td>
          <td>@php echo strtoupper($data['class']->name) . " " .  strtoupper($data['class']->arm) @endphp</td>
          <td>Next Term Fee</td>
          <td>Term Ends</td>
          <td>Next Term Begins</td>
        </tr>
        <tr>
          <td>TERM:</td>
          <td>@php echo strtoupper(convertTerm(getConfigValue($school, "term"))) @endphp</td>
          <td>
            @php echo 'NGN'.number_format($data['class']->fee,2) @endphp <br/>
            @if($data['class']->amount != 0 && $data['class']->amount != null && $data['class']->amount != "")
              @php echo $data['class']->other_payment_title ; @endphp
              @php echo 'NGN'.number_format($data['class']->amount,2) @endphp <br/>
              @php echo 'Total: NGN'.number_format(($data['class']->amount + $data['class']->fee), 2)@endphp
            @endif
          </td>
          <td>@php echo ( getConfigValue($school ,'term_ends') == "" ? 'N/A' : _date(getConfigValue($school, 'term_ends') )) @endphp</td>
          <td>@php echo ( getConfigValue($school, 'next_term_begins') == "" ? 'N/A' : _date(getConfigValue($school, 'next_term_begins')) ) @endphp</td>
        </tr>
      </table>

      <table style="width:100%;" border="1">
        <tr>
          <th colspan="3"><center>KEYS TO RATINGS ON OBSERVABLE BEHAVIOUR</center></th>
        </tr>
        <tr>
          <td> 5.) Maintains an excellent decree of observable traits </td>
          <td> 4.) Maintains high level of observable traits</td>
          <td> 3.) Acceptable level of observable traits</td>
        </tr>
        <tr>
          <td> 2.) Shows minimal regards for observable traits </td>
          <td colspan="2"> 1.) Has no regard for observable traits</td>
        </tr>
      </table>


    {{--
        rating
    --}}
    @include("results.fkka.includes.ratings.rate")

    {{--
        Grade
    --}}
    @if($data['class']->section == "junior")
        @include("results.fkka.includes.grade.junior")
    @elseif ($data['class']->section == "senior")
        @include("results.fkka.includes.grade.senior")
    @elseif ($data['class']->section == "primary")
        @include("results.fkka.includes.grade.primary")
    @elseif ($data['class']->section == "nursery")
        @include("results.fkka.includes.grade.nursery")
    @endif

    {{--
        Report card
    --}}
    @if($data['class']->section == "junior")
        @include("results.fkka.includes.report-card.junior")
    @elseif ($data['class']->section == "senior")
        @include("results.fkka.includes.report-card.senior")
    @elseif ($data['class']->section == "primary")
        @include("results.fkka.includes.report-card.primary")
    @elseif ($data['class']->section == "nursery")
        @include("results.fkka.includes.report-card.nursery")
    @endif


      <table style="width:100%;" border="1">
        <tr>
          <th colspan="5"><center>REMARK AND CONCLUSION</center></th>
        </tr>
        <tr>
          <td> Class No. @php echo $data['no_of_students'] @endphp </td>
          <td> Class Avg. @php echo optional($data['grade'])->ca @endphp</td>
          <td> H. Avg. @php echo optional($data['grade'])->high @endphp</td>
          <td> L. Avg. @php echo optional($data['grade'])->low @endphp</td>
          <td> Grade @php echo optional($data['grade'])->grade @endphp</td>
        </tr>
        <tr>
          <td> Percentage {{ optional($data['grade'])->perc }}</td>
          <td> No. of Subject {{ optional($data['grade'])->ns }}</td>
          <td> Mark Obtained {{ optional($data['grade'])->mk1 }}</td>
          <td> Mark Obtainable  {{  optional($data['grade'])->mk2 }}</td>
          <td> POSITION {{  optional($data['grade'])->pos }}</td>
        </tr>
      </table>

      <table style="width:100%;" border="1">
        <tr>
          <th><center>COMMENTS</center></th>
          <th><center>SIGNATURE/STAMP</center></th>
        </tr>
        <tr>
          <td>
            <span>Class Teacher's Comments:</span>
            <div class="t_comment">
               {{ $data['student']->first_name .' '. optional($data['grade'])->t_remark; }}
            </div>
          </td>
          <td rowspan="2">
            <center>
              <img src="@php echo getConfigValue($school,'school_signature'); @endphp" alt="@php echo getConfigValue($school ,'school_name') @endphp" width="100"/>
            </center>
          </td>
        </tr>
        <tr>
          <td>
            <span>Principal's Comments</span>
            <div class="p_comment">
            @php echo optional($data['grade'])->p_remark; @endphp
            </div>
          </td>
        </tr>

      </table>

    <div>

      <p>
        <center>
          <b>Printed Date: {{ date('l jS \of F Y h:i:s A'); }}</b><br>
          Proudly Powered by Codepym @ www.codepym.com
        </center>
      </p>
    </div>
    </div>
    <style>
        @page{
          margin: 0cm 0.5cm 0cm 0.5cm;
        }

        #watermark {
            position: fixed;
            bottom:   0px;
            left:     0px;
            top: 0px;

            opacity: 0.2;
            z-index:  -1000;
        }

        .school_logo img, .student_image img{
          width:1cm;
        }
        td{
          padding: 2px;
        }
        table{
          margin-top: 2px;
          font-family: 'Heebo', sans-serif;
        }
        table, tr{
          border-collapse: collapse;
          font-size: 11px;
        }
        th{
          background: #ddd;
          padding: 2px;
        }

        .result-wrapper{
              margin: 0.1cm 1cm 0.1cm 0.1cm;
              padding: 0.5cm !important;
          }
          .p_comment, .t_comment{
            border:2px dashed black;
            padding:5px;
          }
    </style>
</div>
