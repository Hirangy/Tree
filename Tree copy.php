<!DOCTYPE html>
<?php
//FAI's Features
//-Import/Export
//-Share
//-Input Field:(Image/Title/Firstname/Middlename/Lastname/Nickname/Birth/Death/Contact/etc)
//-Mouseover Tools:(Info/Edit/Focus/Add:(Parent/Child/Spouse))
//-Notification
//-Social
//-Filters(CloseFamily/Descendants/ExtendedFamily)
//-Listview
//-Search


//-Member System


//Setup
$csv = array_map('str_getcsv', file('British Royal.csv'));
$yk = 0;
$datapoints = 18;
$resizing = 10;
$start = ($_GET["start"]);
$focus = [$start];
$scale = 40;
$line = [];
array();

foreach($csv as $location => $data)
{
  for($x = 0; $x < $datapoints; $x++){
  $array[$yk][$x]=$data[$x];
  }
  $yk++;
}
array_shift($array);
$yk=sizeof($array);

for($k=0;$k<$yk;$k++){

  while (strlen($array[$k][9])<16) {
    $array[$k][9] .= " ";
    $array[$k][9] = " " . $array[$k][9];
  }
  while (strlen($array[$k][10])<20) {
    $array[$k][10] .= " ";
    $array[$k][10] = " " . $array[$k][10];
  }
}

?>
<html>
<head>
  <style>
  @font-face {
    font-family: Silapakorn-Bold;
    src: url(silapakorn72-bold_beta05.ttf);
    font-family: Silapakorn-Regular;
    src: url(silapakorn72-regular_beta05.ttf);
  }
  table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
 body {
   padding: 0;
   margin: 0;
}
* {
  padding: 0;
  margin: 0;
  outline: 0;
  overflow: hidden;
}
html, body {
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
}
#zoom {
  width: 100%;
  height: 100%;
  transform-origin: 0px 0px;
  transform: scale(1) translate(0px, 0px);
  cursor:  url('Tree Cursor.svg'), auto;
}
svg#zoom {
  width: 100%;
  height: auto;
}
</style>
</head>

<!-- UNmodified Database Table
<table style="position:relative; top:0%; left:0%;">
  <tr>
    Unmodified Database
  </tr>
  <tr>
    <th>[0]ID</th>
    <th>[1]Father</th>
    <th>[2]Mother</th>
    <th>[3]Spouse</th>
    <th>[4]Xpos</th>
    <th>[5]Ypos</th>
    <th>[6]Sex</th>
    <th>[7]Ancestors</th>
  </tr>
  <?php for($k = 0; $k < $yk; $k++){
  echo  "<tr>";
  for($x = 0; $x < $datapoints; $x++){
    echo "<th>",$array[$k][$x],"</th>";
  }
  echo  "</tr>";
};?>
</table>
-->
<?php
//Array
for($k=0;$k<$yk;$k++) {
  //Parenting Children
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][1]){
        $array[$kk][3] = $array[$k][2];
      }
      if($array[$kk][0]==$array[$k][2]){
        $array[$kk][3] = $array[$k][1];
      }
    }
}
//Pairing Spouse
for($k=0;$k<$yk;$k++) {
  if($array[$k][3]!=0) {
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][3]){
        $array[$kk][3] = $array[$k][0];
      }
    }
  }
}

//Node
$yn=0;
for($k=0;$k<$yk;$k++) {
  //Setup
  if($array[$k][3]!=0){
  $node[$yn][0]=$yn;
  $node[$yn][1]=$array[$k][0];
  $node[$yn][2]=$array[$k][3];
  $node[$yn][3]=-100;
  $node[$yn][4]=-100;
  $node[$yn][5]=0;
  //Spouse-side Culling
  for($kk=0;$kk<$yn;$kk++){
    if($node[$kk][1]==$array[$k][0]||$node[$kk][2]==$array[$k][0]){
      array_pop($node);
      continue 2;
    }
  }
  //Adding and Sizing for Children
  for($kk=0;$kk<$yk;$kk++){
    if($array[$kk][1]==$node[$yn][1]||$array[$kk][2]==$node[$yn][1]||$array[$kk][1]==$node[$yn][2]||$array[$kk][2]==$node[$yn][2]){
      array_push ($node[$yn] , $array[$kk][0]);
      $node[$yn][5]++;
    }
  }
  if($node[$yn][5]<2){
    $node[$yn][5]=2;
  }
  $yn++;
  }
}
//Node Resizing
for($a = 0; $a < $resizing; $a++){
for($n = 0; $n < $yn; $n++){
  $node[$n][5]=0;
  for($nn=6;$nn<sizeof($node[$n]);$nn++){
    for($nnn=0;$nnn<$yn;$nnn++){
      if($node[$nnn][1]==$node[$n][$nn]||$node[$nnn][2]==$node[$n][$nn]){
       $node[$n][5]+=$node[$nnn][5]-1;
      }
    }
    $node[$n][5]+=1;
  }
  if($node[$n][5]<2){
    $node[$n][5]=2;
  }
}
}

//BranchDown
for($k=0;$k<$yk;$k++){
  $array[$k][7]=1;
}
for($a=0;$a<$resizing;$a++){
for($k=0;$k<$yk;$k++){
  //Setup
  $siblings=1;
  $parents=0;
  //Add Siblings
  for($kk=0;$kk<$yk;$kk++){
    if($array[$kk][1]==$array[$k][1]&&$array[$kk][1]!=0&&$array[$kk][1]!=0){
      if($kk!=$k){
        $siblings++;
      }
    }
  }
  //Add Parents
    if($array[$k][1]!=0){
      for($kk=0;$kk<$yk;$kk++){
        if($array[$kk][0]==$array[$k][1]){
          $parents+=$array[$kk][7];
        }
      }
    }
    if($array[$k][2]!=0){
      for($kk=0;$kk<$yk;$kk++){
        if($array[$kk][0]==$array[$k][2]){
          $parents+=$array[$kk][7];
        }
      }
    }
    //Finallize
    $array[$k][7] = max($siblings,$parents);
}
}

function BranchDown($protagonist,$yk,$yn,$array,$node,$line) {
 global $focus, $yk, $yn, $array, $node, $line;
 $protagonist=end($focus);
//Protagonist's Spouse
for($k=0;$k<$yk;$k++) {
  if($array[$k][3]==$protagonist){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$protagonist){
        $array[$k][4]=$array[$kk][4]+1;
        $array[$k][5]=$array[$kk][5];
      }
    }
  }
}
//Protagonist's Node
for($n=0;$n<$yn;$n++) {
  if($node[$n][1]==$protagonist||$node[$n][2]==$protagonist){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$protagonist){
        $node[$n][3]=$array[$kk][4]+0.5;
        $node[$n][4]=$array[$kk][5]-0.5;
        array_push ($line, [$array[$kk][4],-$array[$kk][5],$array[$kk][4]+1,-$array[$kk][5]]);
        if(sizeof($node[$n])>6){
          array_push ($line, [$array[$kk][4]+0.5,-$array[$kk][5],$array[$kk][4]+0.5,-$array[$kk][5]+0.5]);
        }
      }
    }
  }
}
//Protagonist Children
for($n=0;$n<$yn;$n++){
  if($node[$n][1]==$protagonist||$node[$n][2]==$protagonist){
    //Place Children Under Node
    for($k=0;$k<$yk;$k++){
      if($array[$k][1]==$node[$n][1]||$array[$k][2]==$node[$n][1]||$array[$k][1]==$node[$n][2]||$array[$k][2]==$node[$n][2]){
        $array[$k][4]=$node[$n][3]-($node[$n][5]-1)/2;
        $array[$k][5]=$node[$n][4]-0.5;
      }
    }
    //Move Children Along Node
    $c=0;
    for($k=0;$k<$yk;$k++){
      if($array[$k][1]==$node[$n][1]||$array[$k][2]==$node[$n][1]||$array[$k][1]==$node[$n][2]||$array[$k][2]==$node[$n][2]){
        //Yes Family
        for($nn=0;$nn<$yn;$nn++){
          if($array[$k][0]==$node[$nn][1]||$array[$k][0]==$node[$nn][2]){
              $array[$k][4]+=($node[$nn][5]-1)/2-0.5+$c;
              $node[$nn][3]=$array[$k][4]+0.5;
              $node[$nn][4]=$array[$k][5]-0.5;
              array_push($focus, $array[$k][0]);
              if (sizeof($focus)<100){
                BranchDown($focus,$yk,$yn,$array,$node,$line);
                }
              array_push($line, [$array[$k][4],-$array[$k][5],$array[$k][4],-$array[$k][5]-0.5]);
              $c+=$node[$nn][5];
              continue 2;
          }
        }
        //No Family
        if(sizeof($node[$n])==7){
          $array[$k][4]+=0.5;
        }
        $array[$k][4]+=$c;
        $c++;
        array_push ($line, [$array[$k][4],-$array[$k][5],$array[$k][4],-$array[$k][5]-0.5]);

      }
    }
  }
}
}

//SELF
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$focus[0]){
    if ($array[$k][1]==0){
      for($kk=0;$kk<$yk;$kk++) {
        if($array[$kk][0]==$focus[0]){
          $array[$kk][4]=0;
          $array[$kk][5]=0;
        }
      }
      BranchDown($focus,$yk,$yn,$array,$node,$line);
    } else {
      $focus[0]=$array[$k][1];
      for($kk=0;$kk<$yk;$kk++) {
        if($array[$kk][0]==$focus[0]){
          $array[$kk][4]=0;
          $array[$kk][5]=0;
        }
      }
      BranchDown($focus,$yk,$yn,$array,$node,$line);
      $offsetX = $array[$k][4];
      $offsetY = $array[$k][5];
      for($K=0;$K<$yk;$K++){
        $array[$K][4]-=$offsetX;
        $array[$K][5]-=$offsetY;
      }
      for($K=0;$K<$yn;$K++){
        $node[$K][3]-=$offsetX;
        $node[$K][4]-=$offsetY;
      }
      for($K=0;$K<sizeof($line);$K++){
        $line[$K][0]-=$offsetX;
        $line[$K][2]-=$offsetX;
        $line[$K][1]+=$offsetY;
        $line[$K][3]+=$offsetY;
      }
      break;
    }
  }
}

//PARENTS
//Paternal Family
$focus=[$start];
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$focus[0]){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$k][1]==$array[$kk][0]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kk][1]==$array[$kkk][0]){
            array_push($line,[$array[$kk][4],-$array[$kk][5],$array[$kk][4],-$array[$kk][5]-0.5]);
            for($n=0;$n<$yn;$n++){
              if($node[$n][1]==$array[$kkk][0]||$node[$n][2]==$array[$kkk][0]){
                for($nn=0;$nn<$yn;$nn++){
                  if($node[$nn][1]==$array[$kk][0]||$node[$nn][2]==$array[$kk][0]){
                    $array[$kkk][4]=$array[$kk][4]-(($node[$n][5])/2)+(($node[$nn][5])/2)-0.5;
                  }
                }
                $array[$kkk][5]=$array[$kk][5]+1;
                //       array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4]+1,-$array[$kkk][5]]);
                //array_push($line,[$array[$kkk][4]+0.5,-$array[$kkk][5],$array[$kkk][4]+0.5,-$array[$kkk][5]+0.5]);
                $node[$n][3]=$array[$kkk][4]+0.5;
                $node[$n][4]=$array[$kkk][5]-0.5;
                $c=0;
                for($kkkk=0;$kkkk<$yk;$kkkk++){
                  for($nn=6;$nn<sizeof($node[$n]);$nn++){
                    if($array[$kkkk][0]==$node[$n][$nn]){
                      if($array[$kk][0]!=$array[$kkkk][0]){
                        for($nnn=0;$nnn<$yn;$nnn++){
                          if($node[$nnn][1]==$array[$kkkk][0]||$node[$nnn][2]==$array[$kkkk][0]){
                            $array[$kkkk][4]=$node[$n][3]-($node[$n][5]-2)/2+$c;
                            for($nnnn=0;$nnnn<$yn;$nnnn++){
                              if($node[$nnnn][1]==$array[$kkkk][0]||$node[$nnnn][2]==$array[$kkkk][0]){
                                if($node[$nnnn][5]>2){
                                  $array[$kkkk][4]+=($node[$nnnn][5]-2)/2;
                                }
                              }
                            }
                            $array[$kkkk][5]=$node[$n][4]-0.5;
                            array_push($line,[$array[$kkkk][4],-$array[$kkkk][5],$array[$kkkk][4],-$array[$kkkk][5]-0.5]);
                            $c+=$node[$nnn][5];
                            array_push($focus, $array[$kkkk][0]);
                            BranchDown($focus,$yk,$yn,$array,$node,$line);
                            continue 2;
                          }
                        }
                        $array[$kkkk][4]=$node[$n][3]-($node[$n][5]-2)/2+$c;
                        $array[$kkkk][5]=$node[$n][4]-0.5;
                        array_push($line,[$array[$kkkk][4],-$array[$kkkk][5],$array[$kkkk][4],-$array[$kkkk][5]-0.5]);
                        $c++;
                      }
                    }
                  }
                }
              }
            }
          } else if ($array[$kk][2]==$array[$kkk][0]) {
            for($n=0;$n<$yn;$n++){
              if($node[$n][1]==$array[$kkk][0]||$node[$n][2]==$array[$kkk][0]){
                for($nn=0;$nn<$yn;$nn++){
                  if($node[$nn][1]==$array[$kk][0]||$node[$nn][2]==$array[$kk][0]){
                    $array[$kkk][4]=$array[$kk][4]-(($node[$n][5])/2)+(($node[$nn][5])/2)+0.5;
                  }
                }
                $array[$kkk][5]=$array[$kk][5]+1;
              }
            }
          }
        }
      }
    }
  }
}
//Maternal Family
$focus=[$start];
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$focus[0]){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$k][2]==$array[$kk][0]){

        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kk][1]==$array[$kkk][0]){
            array_push($line,[$array[$kk][4],-$array[$kk][5],$array[$kk][4],-$array[$kk][5]-0.5]);
            for($n=0;$n<$yn;$n++){
              if($node[$n][1]==$array[$kkk][0]||$node[$n][2]==$array[$kkk][0]){
                for($nn=0;$nn<$yn;$nn++){
                  if($node[$nn][1]==$array[$kk][0]||$node[$nn][2]==$array[$kk][0]){
                    $array[$kkk][4]=$array[$kk][4]+(($node[$n][5])/2)-(($node[$nn][5])/2)-0.5;
                  }
                }
                $array[$kkk][5]=$array[$kk][5]+1;
                //array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4]+1,-$array[$kkk][5]]);
                //array_push($line,[$array[$kkk][4]+0.5,-$array[$kkk][5],$array[$kkk][4]+0.5,-$array[$kkk][5]+0.5]);
                $node[$n][3]=$array[$kkk][4]+0.5;
                $node[$n][4]=$array[$kkk][5]-0.5;
                $c=0;
                for($kkkk=0;$kkkk<$yk;$kkkk++){
                  for($nn=6;$nn<sizeof($node[$n]);$nn++){
                    if($array[$kkkk][0]==$node[$n][$nn]){
                      if($array[$kk][0]!=$array[$kkkk][0]){
                        for($nnn=0;$nnn<$yn;$nnn++){
                          if($node[$nnn][1]==$array[$kk][0]||$node[$nnn][2]==$array[$kk][0]){
                            for($nnnn=0;$nnnn<$yn;$nnnn++){
                              if($node[$nnnn][1]==$array[$kkkk][0]||$node[$nnnn][2]==$array[$kkkk][0]){
                                $array[$kkkk][4]=$array[$kk][4]+($node[$nnn][5])/2+($node[$nnnn][5]-2)/2+$c;
                                continue 2;
                              } else {
                                  $array[$kkkk][4]=$array[$kk][4]+($node[$nnn][5])/2+$c;
                                }
                              }
                            }
                        }
                        $array[$kkkk][5]=$node[$n][4]-0.5;
                        array_push($line,[$array[$kkkk][4],-$array[$kkkk][5],$array[$kkkk][4],-$array[$kkkk][5]-0.5]);
                        for($nnn=0;$nnn<$yn;$nnn++){
                          if($node[$nnn][1]==$array[$kkkk][0]||$node[$nnn][2]==$array[$kkkk][0]){
                            $c+=$node[$nnn][5];
                            array_push($focus, $array[$kkkk][0]);
                            BranchDown($focus,$yk,$yn,$array,$node,$line);
                            continue 2;
                          }
                        }
                        $c++;
                      }
                    }
                  }
                }
              }
            }
          } else if ($array[$kk][2]==$array[$kkk][0]) {
            for($n=0;$n<$yn;$n++){
              if($node[$n][1]==$array[$kkk][0]||$node[$n][2]==$array[$kkk][0]){
                for($nn=0;$nn<$yn;$nn++){
                  if($node[$nn][1]==$array[$kk][0]||$node[$nn][2]==$array[$kk][0]){
                    $array[$kkk][4]=$array[$kk][4]+(($node[$n][5])/2)-(($node[$nn][5])/2)+0.5;
                  }
                }
                $array[$kkk][5]=$array[$kk][5]+1;
              }
            }
          }
        }
      }
    }
  }
}
//siblings
for($n=0;$n<$yn;$n++){
  $xmin=$node[$n][3];
  $xmax=$node[$n][3];

  for($nn=6;$nn<sizeof($node[$n]);$nn++){
    for($k=0;$k<$yk;$k++){
      if($array[$k][0]==$node[$n][$nn]){
        if($xmin>$array[$k][4]){
          $xmin=$array[$k][4];
        }
        if($xmax<$array[$k][4]){
          $xmax=$array[$k][4];
        }
      }
    }
  }
  if(sizeof($node[$n])>6){
  array_push ($line, [$xmin,-$node[$n][4],$xmax,-$node[$n][4]]);
  }
}

//GRANDPARENTS
//Paternal Grandmother's Silbings
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$start){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][1]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kkk][0]==$array[$kk][2]){
            $array[$kkk][4]=$array[$kk][4]-$array[$kkk][7]+1;
            $array[$kkk][5]=$array[$kk][5]+1;
            $j = 0;
            for($i=0;$i<$yk;$i++){
              if($array[$i][1]==$array[$kkk][1]&&$array[$i][1]!=0&&$array[$kkk][1]!=0&&$i!=$kkk){
                $j++;
                $array[$i][4]=$array[$kkk][4]+$j;
                $array[$i][5]=$array[$kkk][5];
                array_push($line,[$array[$i][4],-$array[$i][5],$array[$i][4],-$array[$i][5]-0.5]);
                array_push($line,[$array[$i][4],-$array[$i][5]-0.5,$array[$i][4]-1,-$array[$i][5]-0.5]);
              }
            }
            if($array[$kkk][1]!=0){
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4],-$array[$kkk][5]-0.5]);
            }
          }
        }
      }
    }
  }
}
//Paternal Grandfather's Silbings
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$start){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][1]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kkk][0]==$array[$kk][1]){
            for($kkkk=0;$kkkk<$yk;$kkkk++){
              if($array[$kkkk][0]==$array[$kkk][3]){
                $array[$kkk][4]=$array[$kkkk][4]-1;
                $array[$kkk][5]=$array[$kkkk][5];
              }
            }
            $j = 0;
            for($i=0;$i<$yk;$i++){
              if($array[$i][1]==$array[$kkk][1]&&$array[$i][1]!=0&&$array[$kkk][1]!=0&&$i!=$kkk){
                $j++;
                $array[$i][4]=$array[$kkk][4]-$j;
                $array[$i][5]=$array[$kkk][5];
                array_push($line,[$array[$i][4],-$array[$i][5],$array[$i][4],-$array[$i][5]-0.5]);
                array_push($line,[$array[$i][4],-$array[$i][5]-0.5,$array[$i][4]+1,-$array[$i][5]-0.5]);
              }
            }
            /*
            if($array[$kkk][1]!=0){
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4],-$array[$kkk][5]-0.5]);
            }
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4]+1,-$array[$kkk][5]]);
            array_push($line,[$array[$kkk][4]+0.5,-$array[$kkk][5],$array[$kkk][4]+0.5,-$array[$kkk][5]+0.5]);
            array_push($line,[$array[$kk][4],-$array[$kk][5]-0.5,$array[$kkk][4]+0.5,-$array[$kkk][5]+0.5]);
            */
          }
        }
      }
    }
  }
}
//Maternal Grandfather's Silbings
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$start){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][2]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kkk][0]==$array[$kk][1]){
            $array[$kkk][4]=$array[$kk][4]+$array[$kkk][7]-1;
            $array[$kkk][5]=$array[$kk][5]+1;
            $j = 0;
            for($i=0;$i<$yk;$i++){
              if($array[$i][1]==$array[$kkk][1]&&$array[$i][1]!=0&&$array[$kkk][1]!=0&&$i!=$kkk){
                $j++;
                $array[$i][4]=$array[$kkk][4]-$j;
                $array[$i][5]=$array[$kkk][5];
                array_push($line,[$array[$i][4],-$array[$i][5],$array[$i][4],-$array[$i][5]-0.5]);
                array_push($line,[$array[$i][4],-$array[$i][5]-0.5,$array[$i][4]+1,-$array[$i][5]-0.5]);
              }
            }
            if($array[$kkk][1]!=0){
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4],-$array[$kkk][5]-0.5]);
            }

          }
        }
      }
    }
  }
}
//Maternal Grandmother's Silbings
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$start){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][2]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kkk][0]==$array[$kk][2]){
            $array[$kkk][4]=$array[$kk][4]+$array[$kk][7]-$array[$kkk][7];
            $array[$kkk][5]=$array[$kk][5]+1;
            $j = 0;
            for($i=0;$i<$yk;$i++){
              if($array[$i][1]==$array[$kkk][1]&&$array[$i][1]!=0&&$array[$kkk][1]!=0&&$i!=$kkk){
                $j++;
                $array[$i][4]=$array[$kkk][4]+$j;
                $array[$i][5]=$array[$kkk][5];
                array_push($line,[$array[$i][4],-$array[$i][5],$array[$i][4],-$array[$i][5]-0.5]);
                array_push($line,[$array[$i][4],-$array[$i][5]-0.5,$array[$i][4]-1,-$array[$i][5]-0.5]);
              }
            }
            /*
            if($array[$kkk][1]!=0){
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4],-$array[$kkk][5]-0.5]);
            }
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4]-1,-$array[$kkk][5]]);
            array_push($line,[$array[$kkk][4]-0.5,-$array[$kkk][5],$array[$kkk][4]-0.5,-$array[$kkk][5]+0.5]);
            array_push($line,[$array[$kk][4],-$array[$kk][5]-0.5,$array[$kkk][4]-0.5,-$array[$kkk][5]+0.5]);
            */
          }
        }
      }
    }
  }
}

//GREATS
//roundup
$all=[0];
$all[0]=$start;
for($i=0;$i<100;$i++) {
  for($k=0;$k<$yk;$k++){
    if(in_array($array[$k][0], $all)){
      if($array[$k][1]!=0){
        if(!in_array($array[$k][1], $all)){
          array_push($all,$array[$k][1]);
          }
      }
      if($array[$k][2]!=0){
        if(!in_array($array[$k][2], $all)){
          array_push($all,$array[$k][2]);
          }
      }
    }
  }
}

for($x=0;$x<sizeof($all);$x++){
//Paternal Grandmother's Silbings
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$all[$x]){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][1]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kkk][0]==$array[$kk][2]){
            $array[$kkk][4]=$array[$kk][4]-$array[$kkk][7]+1;
            $array[$kkk][5]=$array[$kk][5]+1;
            $j = 0;
            for($i=0;$i<$yk;$i++){
              if($array[$i][1]==$array[$kkk][1]&&$array[$i][1]!=0&&$array[$kkk][1]!=0&&$i!=$kkk){
                $j++;
                $array[$i][4]=$array[$kkk][4]+$j;
                $array[$i][5]=$array[$kkk][5];
                array_push($line,[$array[$i][4],-$array[$i][5],$array[$i][4],-$array[$i][5]-0.5]);
                array_push($line,[$array[$i][4],-$array[$i][5]-0.5,$array[$i][4]-1,-$array[$i][5]-0.5]);
              }
            }
            if($array[$kkk][1]!=0){
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4],-$array[$kkk][5]-0.5]);
            }
          }
        }
      }
    }
  }
}
//Paternal Grandfather's Silbings
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$all[$x]){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][1]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kkk][0]==$array[$kk][1]){
            for($kkkk=0;$kkkk<$yk;$kkkk++){
              if($array[$kkkk][0]==$array[$kkk][3]){
                $array[$kkk][4]=$array[$kkkk][4]-1;
                $array[$kkk][5]=$array[$kkkk][5];
              }
            }
            $j = 0;
            for($i=0;$i<$yk;$i++){
              if($array[$i][1]==$array[$kkk][1]&&$array[$i][1]!=0&&$array[$kkk][1]!=0&&$i!=$kkk){
                $j++;
                $array[$i][4]=$array[$kkk][4]-$j;
                $array[$i][5]=$array[$kkk][5];
                array_push($line,[$array[$i][4],-$array[$i][5],$array[$i][4],-$array[$i][5]-0.5]);
                array_push($line,[$array[$i][4],-$array[$i][5]-0.5,$array[$i][4]+1,-$array[$i][5]-0.5]);
              }
            }
            if($array[$kkk][1]!=0){
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4],-$array[$kkk][5]-0.5]);
            }
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4]+1,-$array[$kkk][5]]);
            array_push($line,[$array[$kkk][4]+0.5,-$array[$kkk][5],$array[$kkk][4]+0.5,-$array[$kkk][5]+0.5]);
            array_push($line,[$array[$kk][4],-$array[$kk][5]-0.5,$array[$kkk][4]+0.5,-$array[$kkk][5]+0.5]);
          }
        }
      }
    }
  }
}
//Maternal Grandfather's Silbings
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$all[$x]){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][2]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kkk][0]==$array[$kk][1]){
            $array[$kkk][4]=$array[$kk][4]+$array[$kkk][7]-1;
            $array[$kkk][5]=$array[$kk][5]+1;
            $j = 0;
            for($i=0;$i<$yk;$i++){
              if($array[$i][1]==$array[$kkk][1]&&$array[$i][1]!=0&&$array[$kkk][1]!=0&&$i!=$kkk){
                $j++;
                $array[$i][4]=$array[$kkk][4]-$j;
                $array[$i][5]=$array[$kkk][5];
                array_push($line,[$array[$i][4],-$array[$i][5],$array[$i][4],-$array[$i][5]-0.5]);
                array_push($line,[$array[$i][4],-$array[$i][5]-0.5,$array[$i][4]+1,-$array[$i][5]-0.5]);
              }
            }
            if($array[$kkk][1]!=0){
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4],-$array[$kkk][5]-0.5]);
            }

          }
        }
      }
    }
  }
}
//Maternal Grandmother's Silbings
for($k=0;$k<$yk;$k++){
  if($array[$k][0]==$all[$x]){
    for($kk=0;$kk<$yk;$kk++){
      if($array[$kk][0]==$array[$k][2]){
        for($kkk=0;$kkk<$yk;$kkk++){
          if($array[$kkk][0]==$array[$kk][2]){
            for($kkkk=0;$kkkk<$yk;$kkkk++){
              if($array[$kkkk][0]==$array[$kkk][3]){
                $array[$kkk][4]=$array[$kkkk][4]+1;
                $array[$kkk][5]=$array[$kkkk][5];
              }
            }
            $j = 0;
            for($i=0;$i<$yk;$i++){
              if($array[$i][1]==$array[$kkk][1]&&$array[$i][1]!=0&&$array[$kkk][1]!=0&&$i!=$kkk){
                $j++;
                $array[$i][4]=$array[$kkk][4]+$j;
                $array[$i][5]=$array[$kkk][5];
                array_push($line,[$array[$i][4],-$array[$i][5],$array[$i][4],-$array[$i][5]-0.5]);
                array_push($line,[$array[$i][4],-$array[$i][5]-0.5,$array[$i][4]-1,-$array[$i][5]-0.5]);
              }
            }
            if($array[$kkk][1]!=0){
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4],-$array[$kkk][5]-0.5]);
            }
            array_push($line,[$array[$kkk][4],-$array[$kkk][5],$array[$kkk][4]-1,-$array[$kkk][5]]);
            array_push($line,[$array[$kkk][4]-0.5,-$array[$kkk][5],$array[$kkk][4]-0.5,-$array[$kkk][5]+0.5]);
            array_push($line,[$array[$kk][4],-$array[$kk][5]-0.5,$array[$kkk][4]-0.5,-$array[$kkk][5]+0.5]);
          }
        }
      }
    }
  }
}
}

?>
<!-- Modified Database Table-
<table style="position:absolute; top:0%; left:30%;">
  <tr>
    <a>Modified Database</a>
  </tr>
  <tr>
    <th>[0]ID</th>
    <th>[1]Father</th>
    <th>[2]Mother</th>
    <th>[3]Spouse</th>
    <th>[4]Xpos</th>
    <th>[5]Ypos</th>
    <th>[6]Sex</th>
    <th>[7]Ancestors</th>
  </tr>
  <?php for($k = 0; $k < $yk; $k++){
  echo  "<tr>";
  for($x = 0; $x < $datapoints; $x++){
    if ($x == 6) {
      if($array[$k][$x]==0){
        echo "<th>","M","</th>";
      } else {
        echo "<th>","F","</th>";
      }
    } else {
    echo "<th>",$array[$k][$x],"</th>";
    }
  }

  echo  "</tr>";
};?>
</table>
-->
<!-- Nodes Table
<table style="position:absolute; top:0%; left:60%;">
  <tr>
    <a>Nodes</a>
  </tr>
  <tr>
    <th>[0]ID</th>
    <th>[1]Father</th>
    <th>[2]Mother</th>
    <th>[3]Xpos</th>
    <th>[4]Ypos</th>
    <th>[5]Span</th>
    <th>[6]</th>
    <th>[7]</th>
    <th>[8]</th>
    <th>[9]</th>
    <th>[10]</th>
    <th>[11]</th>
    <th>[12]</th>
    <th>[13]</th>

  </tr>
  <?php for($k = 0; $k < $yn; $k++){
  echo  "<tr>";
  for($x = 0; $x < 20; $x++){
    if(isset($node[$k][$x])){
    echo "<th>",$node[$k][$x],"</th>";
    }
  }
  echo  "</tr>";
};?>
</table>

-->
<!--
<a>Ancestry</a>
<br>
<a>
<?php
for($k = 0; $k < $ya; $k++){
  foreach ($ancest[$k] as &$kk) {
      echo $kk." ";
  }
?> <br> <?php
}

//Remove extras
for($k = 0; $k < $yk; $k++){
        if ((abs(fmod($array[$k][4],0.5)))>0){
            $array[$k][4]=10000;
          }
}
?>
-->
</a>
<!-- Display -->
<body style="background-color:#F4F0E4;">
<svg id="zoom" height="200vw" width="400vw" viewBox="0 0 4000 2000" style = "position:relative;">
  <?php for($k = -20; $k < 50; $k++){ ?>
      <line x1="<?php echo 2000+($k*$scale);?>" y1="0" x2="<?php echo 2000+($k*$scale);?>" y2="2000" style="stroke:rgb(250,250,250);stroke-width:2" />
  <?php } ?>
  <line x1="2000" y1="0" x2="2000" y2="2000" style="stroke:rgb(255,255,255);stroke-width:3"/>
  <line x1="0" y1="1000" x2="4000" y2="1000" style="stroke:rgb(255,255,255);stroke-width:3"/>

  <?php for($k = 0; $k < sizeof($line); $k++){?>
      <line x1="<?php echo 2000+($line[$k][0]*$scale);?>" y1="<?php echo 1000+(1.8*$line[$k][1]*$scale);?>" x2="<?php echo 2000+($line[$k][2]*$scale);?>" y2="<?php echo 1000+(1.8*$line[$k][3]*$scale);?>" style="stroke:rgb(17, 50, 77);stroke-width:<?php echo $scale/16 ;?>" stroke-linecap="round" />
  <?php } ?>
  <?php for($k = 0; $k < $yk; $k++){

          echo "<a class=\"link\" onclick=\"refocus(",$array[$k][0],")\" style=\"  cursor:pointer;\">";

            if($array[$k][6]==1){
          ?>
          <image href="Tree Female.svg" x="<?php echo 2000+($array[$k][4]*$scale)-$scale/2.8;?>" y="<?php echo 1000-($array[$k][5]*1.8*$scale+$scale/3);?>" width="<?php echo $scale/1.4;?>"/>
            <svg x="<?php echo 2000+($array[$k][4]*$scale-$scale/3.65);?>" y="<?php echo 1003-($array[$k][5]*1.8*$scale+$scale/2.7);?>" width="<?php echo $scale/1.77;?>" height="<?php echo $scale/1.75;?>" viewBox="0 0 100 100">
                <clipPath id="clipCircle">
                  <circle r="50" cx="50" cy="50"/>
                </clipPath>
                <image href="<?php echo $array[$k][17]?>" width = "100" clip-path="url(#clipCircle)"/>
            </svg>
          <?php } else{
            ?>
            <image href="Tree Male.svg" x="<?php echo 2000+($array[$k][4]*$scale-$scale/2.8);?>" y="<?php echo 1000-($array[$k][5]*1.8*$scale+$scale/2.95);?>" width="<?php echo $scale/1.4;?>"/>
            <svg x="<?php echo 2000+($array[$k][4]*$scale-$scale/4);?>" y="<?php echo 1003.4-($array[$k][5]*1.8*$scale+$scale/3);?>" width="<?php echo $scale/2;?>" height="<?php echo $scale/2;?>" viewBox="0 0 100 100">
              <image href="<?php echo $array[$k][17]?>" width = "100"/>
            </svg>
            <?php  }

            ?>
          <!--<text text-anchor="middle" x="<?php echo 2000+($array[$k][4]*$scale);?>" y="<?php echo 1000-(1.8*$array[$k][5]*$scale)+($scale/10);?>" style="fill: white; font-size:<?php echo $scale/3.2;?>;" font-family="Silapakorn-Bold"> <?php echo $array[$k][0];?></text>-->
          <text textLength='18' lengthAdjust="spacingAndGlyphs" text-anchor="middle" x="<?php echo 2000+($array[$k][4]*$scale);?>" y="<?php echo 1012-(1.8*$array[$k][5]*$scale)+($scale/10);?>" style="fill:#11324D; font-size:<?php echo $scale/11;?>;" font-family="Silapakorn-Regular"> <?php echo $array[$k][9];?></text>
          <text textLength='18' lengthAdjust="spacingAndGlyphs" text-anchor="middle" x="<?php echo 2000+($array[$k][4]*$scale);?>" y="<?php echo 1016-(1.8*$array[$k][5]*$scale)+($scale/10);?>" style="fill:#11324D; font-size:<?php echo $scale/13;?>;" font-family="Silapakorn-Regular"> <?php echo $array[$k][10];?></text>
          <text text-anchor="middle" x="<?php echo 2000+($array[$k][4]*$scale);?>" y="<?php echo 1024-(1.8*$array[$k][5]*$scale)+($scale/10);?>" style="fill:#11324D; font-size:<?php echo $scale/10;?>;" font-family="Silapakorn-Bold"> <?php if($array[$k][13]!=0){echo $array[$k][13];} if($array[$k][16]!=0){echo "-",$array[$k][16];}?></text>
          </a>
          <?php
        }
      for($k = 0; $k < $yn; $k++){
      if(sizeof($node[$k])>6){
      ?>

  <?php }
  }
  ?>
</svg>
 <script>
          var st = <?php echo ($_GET["start"]);?>,
          oldx = <?php if(isset($_GET["x"])){echo ($_GET["x"]);}else{echo"-1000";}?>,
          oldy = <?php if(isset($_GET["y"])){echo ($_GET["y"]);}else{echo"-500";}?>,
          olds = <?php if(isset($_GET["scale"])){echo ($_GET["scale"]);}else{echo"2";}?>;
          var scale = olds,
          panning = false,
          pointX = oldx,
          pointY = oldy,
          start = { x: -1000, y: -500 },
          zoom = document.getElementById("zoom");
          zoom.style.transform = "translate(" + pointX + "px, " + pointY + "px) scale(" + scale + ")";

        function setTransform() {
          zoom.style.transform = "translate(" + pointX + "px, " + pointY + "px) scale(" + scale + ")";
          window.history.pushState('', 'New Page Title', '/Tree/Tree%20copy.php?start='+st+'&x='+pointX+'&y='+pointY+'&scale='+scale);
        }

        zoom.onmousedown = function (e) {
          e.preventDefault();
          start = { x: e.clientX - pointX, y: e.clientY - pointY };
          panning = true;
        }

        zoom.onmouseup = function (e) {
          panning = false;
        }

        zoom.onmousemove = function (e) {
          e.preventDefault();
          if (!panning) {
            return;
          }
          pointX = (e.clientX - start.x);
          pointY = (e.clientY - start.y);
          setTransform();
        }

        zoom.onwheel = function (e) {
          e.preventDefault();
          var xs = (e.clientX - pointX) / scale,
            ys = (e.clientY - pointY) / scale,
            delta = (e.wheelDelta ? e.wheelDelta : -e.deltaY);
          (delta > 0) ? (scale *= 1.2) : (scale /= 1.2);
          pointX = e.clientX - xs * scale;
          pointY = e.clientY - ys * scale;

          setTransform();
        }

        function refocus(k) {
          window.location.href = 'http://localhost/Tree/Tree%20copy.php?start='+k+'&x='+pointX+'&y='+pointY+'&scale='+scale;

        }


      </script>
      <svg style="position:fixed; top:0vw; left:0vw; height: 100%; pointer-events: none" viewBox="0 0 1000 1000" >
        <image href="Tree Toolbar.svg" x="0" y="0" height="1000">
      <svg>
</body>

</html>
