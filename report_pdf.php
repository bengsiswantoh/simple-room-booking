<?php
require_once("include/func_db.php");
require_once("include/fpdf.php");

class PDF_MC_Table extends FPDF
{
  var $width = 8;
  var $height = 5;

  function SetWidths($w)
  {
      //Set the array of column widths
      $this->widths=$w;
  }

  function SetAligns($a)
  {
      //Set the array of column alignments
      $this->aligns=$a;
  }
  
  function ToPDF($db_rooms, $year, $month)
  {
    $sql = "SELECT id, name FROM rooms ORDER BY gender desc, name";
    $rooms = db_select($sql, $db_rooms);

    $year_month = $year."-".$month;

    $max = date("t", strtotime($year_month));
    $start_date = 1;
    
    $data = array();
    
    $height = $this->height;
    $width = $this->width;
    $align = 'C';
    
    $this->print_header($align, $max, $start_date);
   
    foreach ($rooms as $room) 
    {      
      $sql = "SELECT title, end, start FROM bookings ";
      $sql .= "WHERE bookings.room_id='".$room[0]."' ";
      $bookings = db_select($sql, $db_rooms);
      
      //check max height
      $nb = 1;
      
      $temp_nb = $this->NbLines($width * 3, $room[1]);
      
      
      for ($i = $start_date; $i <= $max; $i++) 
      {
        $guest = "";
        $current_date = date("Y-m-d", strtotime($year_month." +".($i - 1) ." day"));
        foreach ($bookings as $booking) 
        {
          $end = $booking[1];
          $start = $booking[2];
          if ($start <= $current_date && $current_date <= $end)
          {
            $guest = $booking[0];
            break;
          }
        }
        
        if ($guest != "")
        {
          if (date("Y-m", strtotime($end)) != $year_month)
          {
            $end = date("Y-m-t", strtotime($year_month));
          }
          if (date("Y-m", strtotime($start)) != $year_month)
          {
            $start = date("Y-m-", strtotime($year_month))."01";
          }
          $colspan = (date("d", strtotime($end)) - date("d", strtotime($start)));
          $i += $colspan;
          $temp_nb = $this->NbLines($width * ( $colspan + 1), $guest);
        }
        if ($nb < $temp_nb)
        {
          $nb = $temp_nb;
        }
      }
      $current_height = $height * $nb;
      
      $this->print_column($current_height, $width * 3, $room[1], $align, $max, $start_date);
      for ($i = $start_date; $i <= $max; $i++) 
      {
        $guest = "";
        $current_date = date("Y-m-d", strtotime($year_month." +".($i - 1) ." day"));
        foreach ($bookings as $booking) 
        {
          $end = $booking[1];
          $start = $booking[2];
          if ($start <= $current_date && $current_date <= $end)
          {
            $guest = $booking[0]; 
            break;
          }
        }
      
        if ($guest == "")
        {
          $this->print_column($current_height, $width, $guest, $align, $max, $start_date);
        }
        else
        {
          if (date("Y-m", strtotime($end)) != $year_month)
          {
            $end = date("Y-m-t", strtotime($year_month));
          }
          if (date("Y-m", strtotime($start)) != $year_month)
          {
            $start = date("Y-m-", strtotime($year_month))."01";
          }
          $colspan = (date("d", strtotime($end)) - date("d", strtotime($start)));
          $i += $colspan;
          
          $this->print_column($current_height, $width * ($colspan + 1), $guest, $align, $max, $start_date, true);
        }
      }
      $this->move_line($current_height);
    }
  }
  
  function print_header($align, $max, $start_date)
  {
    //write kamar
    $this->print_column($this->height * 2, $this->width * 3, "Kamar", $align, $max, $start_date);
    
    //write tanggal
    $this->print_column($this->height, $this->width * $max, "Tanggal", $align, $max, $start_date);
    
    $this->Ln($this->height);
    $x = $this->GetX() + $this->width * 3;
    $y = $this->GetY();
    $this->SetXY($x, $y);
    
    //write date
    for($i = $start_date; $i <= $max; $i++)
    {
      $this->print_column($this->height, $this->width, $i, $align, $max, $start_date);
    }
    
    $this->move_line($this->height);
  }
  
  function print_column($height, $width, $data, $align, $max, $start_date, $fill=false)
  {
    $this->CheckPageBreak($height, $data, $align, $max, $start_date);
    
    $x = $this->GetX();
    $y = $this->GetY();
    if ($fill)
    {
      $this->Rect($x, $y, $width, $height, "DF");
    }
    else
    {
      $this->Rect($x, $y, $width, $height);
    }
    $this->MultiCell($width, 5, $data, 0, $align);
    $this->SetXY($x + $width, $y);
  }
  
  function move_line($height)
  {
    $this->Ln($height);
    $x = $this->GetX();
    $y = $this->GetY();
    $this->SetXY($x, $y);
  }

  function CheckPageBreak($height, $data, $align, $max, $start_date)
  {
      //If the height h would cause an overflow, add a new page immediately
      if($this->GetY() + $height > $this->PageBreakTrigger)
      {
        $this->AddPage($this->CurOrientation);
        $this->print_header($align, $max, $start_date);
      }
  }

  function NbLines($w, $txt)
  {
      //Computes the number of lines a MultiCell of width w will take
      $cw=&$this->CurrentFont['cw'];
      if($w==0)
          $w=$this->w-$this->rMargin-$this->x;
      $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
      $s=str_replace("\r",'',$txt);
      $nb=strlen($s);
      if($nb>0 and $s[$nb-1]=="\n")
          $nb--;
      $sep=-1;
      $i=0;
      $j=0;
      $l=0;
      $nl=1;
      while($i<$nb)
      {
          $c=$s[$i];
          if($c=="\n")
          {
              $i++;
              $sep=-1;
              $j=$i;
              $l=0;
              $nl++;
              continue;
          }
          if($c==' ')
              $sep=$i;
          $l+=$cw[$c];
          if($l>$wmax)
          {
              if($sep==-1)
              {
                  if($i==$j)
                      $i++;
              }
              else
                  $i=$sep+1;
              $sep=-1;
              $j=$i;
              $l=0;
              $nl++;
          }
          else
              $i++;
      }
      return $nl;
  }
}

$month = $_GET["month"];
$year = $_GET["year"];

$pdf=new PDF_MC_Table("L", "mm", "A4");
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(58, 135, 173);
$pdf->ToPDF($db_rooms, $year, $month);
$pdf->Output();
?>