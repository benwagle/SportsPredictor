<?php
$teams=array("bos", "bkn", "ny", "phi", "tor", "gs", "lac", "lal", "phx", "sac", "chi", "cle", "det", "ind", "mil", "dal", "hou", "mem", "no", "sa","atl", "cha", "mia", "orl", "wsh","den", "min", "okc", "por", "utah");
$games=array();

for($n=0; $n<sizeof($teams); $n++)
  {
	$S= "http://espn.go.com/nba/team/schedule/_/name/". $teams[$n] ."/year/2004/seasontype/2/";
	$stuff= file_get_contents($S);
	$gameID= explode("recap?id=", $stuff);
	for($x=1; $x<sizeof($gameID); $x++)
	  {
			$shit= explode('">', $gameID[$x]);
			if(in_array($shit[0], $games))
		 	  {
				continue;
		      }

		    array_push($games, $shit[0]);
	  }
	  
	  if(sizeof($games)!=1230)
	     {
	  		$stuff= file_get_contents($S);
	  		$gameID= explode("boxscore?id=", $stuff);
	  		for($x=1; $x<sizeof($gameID); $x++)
	  		{
				$shit= explode('">', $gameID[$x]);
				if(in_array($shit[0], $games))
		 		  {
					continue;
		     	 }

		   		 array_push($games, $shit[0]);
	  	      }
	     }
   }
for($j=0; $j<sizeof($games); $j++)
   {
		$url= "http://espn.go.com/nba/boxscore?gameId=".$games[$j];
		$content= file_get_contents($url);
		$title= explode("<title>", $content);
		$title= explode("ESPN", $title[1]);
		$title= explode("vs.", $title[0]);
		$awayTeam= $title[0];
		$title= explode("-", $title[1]);
		$homeTeam=$title[0];
   
		
		print($title[2].",");
		$away= explode("<tbody>", $content);
		$away= explode('</tr>', $away[3]);
		$away= explode('<strong>', $away[0]);


		print($awayTeam.",");
		for($l=1; $l<13; $l++)
		{
		if($l>=1 && $l<=3)
		   {
		   $away[$l]= str_replace("-", ",", $away[$l]);
		   }
		print($away[$l]); 
		print(",");
		}

		$home= explode("<tbody>", $content);
		$home= explode('</tr>', $home[6]);
		$home= explode('<strong>', $home[0]);

		print($homeTeam.",");
		for($k=1; $k<13; $k++)
		{
		if($k>=1 && $k<=3)
		   {
		   $home[$k]= str_replace("-", ",", $home[$k]);
		   }
		print($home[$k]);
		print(",");
		}
		
		print("</br>");

    }

?>