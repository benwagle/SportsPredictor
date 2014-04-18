<?php

$user= "root";
$pass= "sexler";
$conn = new PDO('mysql:host=localhost;dbname=NBA_DATA', $user, $pass);

//********************** link fetching code ************************//
$teams=array("bos", "bkn", "ny", "phi", "tor", "gs", "lac", "lal", "phx", "sac", "chi", "cle", "det", "ind", "mil", "dal", "hou", "mem", "no", "sa","atl", "cha", "mia", "orl", "wsh","den", "min", "okc", "por", "utah");
$names=array("Boston Celtics" => 1, "Brooklyn Nets" => 2, "New Jersey Nets" => 2, "New York Knicks" => 3, "Philadelphia 76ers"=> 4, "Toronto Raptors"=>5, "Golden State Warriors"=>6, "Los Angeles Clippers"=>7, "Los Angeles Lakers"=> 8, "Phoenix Suns"=>9, "Sacramento Kings"=>10, "Chicago Bulls"=>11, "Cleveland Cavaliers"=>12, "Detroit Pistons"=>13, "Indiana Pacers"=>14, "Milwaukee Bucks"=>15, "Dallas"=>16, "Dallas Mavericks"=>16, "Houston"=>17, "Houston Rockets"=>17, "Memphis"=>18, "Memphis Grizzlies"=>18, "New Orleans Hornets"=>19, "New Orleans Pelicans"=>19, "San Antonio"=>20,"San Antonio Spurs"=>20, "Atlanta Hawks"=>21, "Charlotte Bobcats"=>22, "Miami Heat"=>23, "Orlando Magic"=>24, "Washington Wizards"=>25, "Denver"=>26, "Denver Nuggets"=>26,"Minnesota"=>27, "Minnesota Timberwolves"=>27, "Seattle SuperSonics"=>28, "Oklahoma City Thunder"=>28, "Portland Trail Blazers"=>29, "Utah"=>30, "Utah Jazz"=>30);

for($n=0; $n<sizeof($teams); $n++)
  {
    $games=array();
	$S= "http://espn.go.com/nba/team/schedule/_/name/". $teams[$n] ."/year/2003/seasontype/2/";
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
	  
	  if(sizeof($games)!=82)
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
   


   
		//****************************************************************//



		//****************   stats pulling code *************************//
		$ins= $conn->prepare("INSERT INTO Matchups (GameID, Date, Home_Team, Away_Team, Result) VALUES (:game, :date, :home, :away, :result)");

		$ins->bindParam(":game", $gm);
		$ins->bindParam(":date", $date);
		$ins->bindParam(":home", $homeTeam);
		$ins->bindParam(":away", $awayTeam);
		$ins->bindParam(":result", $res);

		$statsHome= $conn->prepare("INSERT INTO Raw_Data (GameID, Date, Season, Home_Team, Home_FGM, Home_FGA, Home_FGP, Home_FTM, Home_FTA, Home_FTP, Home_TPM, Home_TPA, Home_TPP,
								Home_OREB, Home_DREB, Home_TREB, Home_ASST, Home_STL, Home_BLK, Home_TRNOVRS, Home_PF, Home_PTS) VALUES (:game, :date, :season, :team, :fgm, :fga, :fgp,
								 :ftm, :fta, :ftp, :tpm, :tpa, :tpp, :oreb, :dreb, :treb, :asst, :stl, :blk, :tos, :pf, :pts)");
								 
		$statsHome->bindParam(":game", $gm);
		$statsHome->bindParam(":date", $date);
		$statsHome->bindParam(":team", $team);
		$statsHome->bindParam(":season", $season);
		$statsHome->bindParam(":fgm", $fgm);
		$statsHome->bindParam(":fga", $fga);
		$statsHome->bindParam(":fgp", $fgp);
		$statsHome->bindParam(":ftm", $ftm);
		$statsHome->bindParam(":fta", $fta);
		$statsHome->bindParam(":ftp", $ftp);
		$statsHome->bindParam(":tpm", $tpm);
		$statsHome->bindParam(":tpa", $tpa);
		$statsHome->bindParam(":tpp", $tpp);
		$statsHome->bindParam(":oreb", $oreb);
		$statsHome->bindParam(":dreb", $dreb);
		$statsHome->bindParam(":treb", $treb);
		$statsHome->bindParam(":asst", $asst);
		$statsHome->bindParam(":stl", $stl);
		$statsHome->bindParam(":blk", $blk);
		$statsHome->bindParam(":tos", $tos);
		$statsHome->bindParam(":pf", $pf);
		$statsHome->bindParam(":pts", $pts);
		
		$statsAway= $conn->prepare("INSERT INTO Raw_Data (GameID, Date, Season, Away_Team, Away_FGM, Away_FGA, Away_FGP, Away_FTM, Away_FTA, Away_FTP, Away_TPM, Away_TPA, Away_TPP,
								Away_OREB, Away_DREB, Away_TREB, Away_ASST, Away_STL, Away_BLK, Away_TRNOVRS, Away_PF, Away_PTS) VALUES (:game, :date, :season, :team, :fgm, :fga, :fgp,
								 :ftm, :fta, :ftp, :tpm, :tpa, :tpp, :oreb, :dreb, :treb, :asst, :stl, :blk, :tos, :pf, :pts)");
						 
		$statsAway->bindParam(":game", $gm);
		$statsAway->bindParam(":date", $date);
		$statsAway->bindParam(":team", $team);
		$statsAway->bindParam(":season", $season);
		$statsAway->bindParam(":fgm", $fgm);
		$statsAway->bindParam(":fga", $fga);
		$statsAway->bindParam(":fgp", $fgp);
		$statsAway->bindParam(":ftm", $ftm);
		$statsAway->bindParam(":fta", $fta);
		$statsAway->bindParam(":ftp", $ftp);
		$statsAway->bindParam(":tpm", $tpm);
		$statsAway->bindParam(":tpa", $tpa);
		$statsAway->bindParam(":tpp", $tpp);
		$statsAway->bindParam(":oreb", $oreb);
		$statsAway->bindParam(":dreb", $dreb);
		$statsAway->bindParam(":treb", $treb);
		$statsAway->bindParam(":asst", $asst);
		$statsAway->bindParam(":stl", $stl);
		$statsAway->bindParam(":blk", $blk);
		$statsAway->bindParam(":tos", $tos);
		$statsAway->bindParam(":pf", $pf);
		$statsAway->bindParam(":pts", $pts);
		
		$updateHome= $conn->prepare("UPDATE Raw_Data SET Home_Team= :team, Home_FGM= :fgm, Home_FGA= :fga, Home_FGP= :fgp, Home_FTM = :ftm, Home_FTA = :fta, Home_FTP = :ftp, Home_TPM = :tpm, Home_TPA = :tpa, Home_TPP = :tpp,
								Home_OREB = :oreb, Home_DREB = :dreb, Home_TREB = :treb, Home_ASST = :asst, Home_STL = :stl, Home_BLK = :blk, Home_TRNOVRS = :tos, Home_PF = :pf , Home_PTS= :pts WHERE GameID= :game");
		
		$updateHome->bindParam(":team", $team);
		$updateHome->bindParam(":fgm", $fgm);
		$updateHome->bindParam(":fga", $fga);
		$updateHome->bindParam(":fgp", $fgp);
		$updateHome->bindParam(":ftm", $ftm);
 		$updateHome->bindParam(":fta", $fta);
 		$updateHome->bindParam(":ftp", $ftp);
		$updateHome->bindParam(":tpm", $tpm);
		$updateHome->bindParam(":tpa", $tpa);
		$updateHome->bindParam(":tpp", $tpp);
		$updateHome->bindParam(":oreb", $oreb);
		$updateHome->bindParam(":dreb", $dreb);
		$updateHome->bindParam(":treb", $treb);
		$updateHome->bindParam(":asst", $asst);
		$updateHome->bindParam(":stl", $stl);
		$updateHome->bindParam(":blk", $blk);
		$updateHome->bindParam(":tos", $tos);
		$updateHome->bindParam(":pf", $pf);
		$updateHome->bindParam(":pts", $pts);
 		$updateHome->bindParam(":game", $gm);
 		
		$updateAway= $conn->prepare("UPDATE Raw_Data SET Away_Team= :team, Away_FGM= :fgm, Away_FGA= :fga, Away_FGP= :fgp, Away_FTM = :ftm, Away_FTA = :fta, Away_FTP = :ftp, Away_TPM = :tpm, Away_TPA = :tpa, Away_TPP = :tpp,
								Away_OREB = :oreb, Away_DREB = :dreb, Away_TREB = :treb, Away_ASST = :asst, Away_STL = :stl, Away_BLK = :blk, Away_TRNOVRS = :tos, Away_PF = :pf , Away_PTS= :pts WHERE GameID= :game");
		
		$updateAway->bindParam(":team", $team);
		$updateAway->bindParam(":fgm", $fgm);
		$updateAway->bindParam(":fga", $fga);
		$updateAway->bindParam(":fgp", $fgp);
		$updateAway->bindParam(":ftm", $ftm);
 		$updateAway->bindParam(":fta", $fta);
 		$updateAway->bindParam(":ftp", $ftp);
		$updateAway->bindParam(":tpm", $tpm);
		$updateAway->bindParam(":tpa", $tpa);
		$updateAway->bindParam(":tpp", $tpp);
		$updateAway->bindParam(":oreb", $oreb);
		$updateAway->bindParam(":dreb", $dreb);
		$updateAway->bindParam(":treb", $treb);
		$updateAway->bindParam(":asst", $asst);
		$updateAway->bindParam(":stl", $stl);
		$updateAway->bindParam(":blk", $blk);
		$updateAway->bindParam(":tos", $tos);
		$updateAway->bindParam(":pf", $pf);
		$updateAway->bindParam(":pts", $pts);
 		$updateAway->bindParam(":game", $gm);
		
		$fgm= 0.0;
		$fga= 0.0;
		$fgp= 0.0;
		$tpm= 0.0;
		$tpa= 0.0;
		$tpp= 0.0;
		$ftm= 0.0;
		$fta= 0.0;
		$ftp= 0.0;
		$oreb= 0.0;
		$dreb= 0.0;
		$treb= 0.0;
		$asst= 0.0;
		$stl= 0.0;
		$blk= 0.0;
		$tos= 0.0;
		$pf= 0.0;
		$pts= 0.0;

		
		$find= $conn->prepare("SELECT GameID FROM Raw_Data WHERE GameID = :game");
		$find->bindParam(":game", $gm);
		

		$months= ["January", "February", "March", "April", "May", "June", "July", "August" , "September", "October", "November", "December"];
		sort($games);
		for($j=0; $j<sizeof($games); $j++)
		   {
				$url= "http://espn.go.com/nba/boxscore?gameId=".$games[$j];
				$content= file_get_contents($url);
				$title= explode("<title>", $content);
				$title= explode("ESPN", $title[1]);
				$title= explode("vs.", $title[0]);
				$awayTeam= trim($title[0]);
				$title= explode("-", $title[1]);
				$homeTeam=trim($title[0]);
		
				$gm= $games[$j];
				$season= 03;
		
				//Change date format for database
				$d = explode(",",$title[2]);
				$year= trim($d[1]);
				$d= explode(" ", $d[0]);
				$day= $d[2];
				$month= array_search($d[1], $months)+1;
				$date= $year."-".$month."-".$day;
				
				$away= explode("<tbody>", $content);
				$away= explode('</tr>', $away[3]);
				$away= explode('<strong>', $away[0]);
				
				$home= explode("<tbody>", $content);
				$home= explode('</tr>', $home[6]);
				$home= explode('<strong>', $home[0]);
				
				
				if($names[$awayTeam] == ($n+1))
				{
				    //print("Away Team");
					//*** AWAY STATS ************//
					//print($date);
					
					$find->execute();
					$can = $find->fetch();
					
					$fgm *= -1;
					$fga *= -1;
					$fgp *= -1;
					$tpm *= -1;
					$tpa *= -1;
					$tpp *= -1;
					$ftm *= -1;
					$fta *= -1;
					$ftp *= -1;
					$oreb *= -1;
					$dreb *= -1;
					$treb *= -1;
					$asst *= -1;
					$stl *= -1;
					$blk *= -1;
					$tos *= -1;
					$pf *= -1;
					$pts *= -1;
					
					if($j>0 && ($can[0] == $gm))
					 {						 
						$updateAway->execute();
					  }
					
					else if($j>0)
					 {
						$statsAway->execute();	
					  }
					  
					    $fgm *= -1;
						$fga *= -1;
						$fgp *= -1;
						$tpm *= -1;
						$tpa *= -1;
						$tpp *= -1;
						$ftm *= -1;
						$fta *= -1;
						$ftp *= -1;
						$oreb *= -1;
						$dreb *= -1;
						$treb *= -1;
						$asst *= -1;
						$stl *= -1;
						$blk *= -1;
						$tos *= -1;
						$pf *= -1;
						$pts *= -1;
					
					$team= $awayTeam;

					$fgs= explode("-", $away[1]);
					$fgm= ($fgm*($j) + (double) $fgs[0])/($j+1);
					$fga= ($fga*($j) +(double) $fgs[1])/($j+1);
					$fgp= ($fgp*($j) + ((double) $fgs[0] )/ (double) $fgs[1])/($j+1);
		
					$tps= explode("-",$away[2]);
					$tpm= ($tpm*($j) + (double) $tps[0])/($j+1);
					$tpa= ($tpa*($j) + (double) $tps[1])/($j+1);
					$tpp= ($tpp*($j) + ((double) $tps[0] )/ (double) $tps[1])/($j+1);
		
					$fts= explode("-", $away[3]);
					$ftm= ($ftm*($j) + (double) $fts[0])/($j+1);
					$fta= ($fta*($j) + (double) $fts[1])/($j+1);
					$ftp= ( $ftp*($j) + ((double) $fts[0] )/ (double) $fts[1])/($j+1);
		
					$oreb= ($oreb*($j) + (double) $away[4])/($j+1);
					$dreb= ($dreb*($j) + (double) $away[5])/($j+1);
					$treb= ($treb*($j) + (double) $away[6])/($j+1);
					$asst= ($asst*($j) + (double) $away[7])/($j+1);
					$stl= ($stl*($j) + (double) $away[8])/($j+1);
					$blk= ($blk*($j) + (double) $away[9])/($j+1);
					$tos= ($tos*($j) + (double) $away[10])/($j+1);
					$pf= ($pf*($j) + (double) $away[11])/($j+1);
					$pts= ($pts*($j) + (double) $away[12])/($j+1);
					
					if($j==0 && ($can[0] == $gm))
					 {
						$fgm *= -1;
						$fga *= -1;
						$fgp *= -1;
						$tpm *= -1;
						$tpa *= -1;
						$tpp *= -1;
						$ftm *= -1;
						$fta *= -1;
						$ftp *= -1;
						$oreb *= -1;
						$dreb *= -1;
						$treb *= -1;
						$asst *= -1;
						$stl *= -1;
						$blk *= -1;
						$tos *= -1;
						$pf *= -1;
						$pts *= -1;
											 
						$updateAway->execute();
						
						$fgm *= -1;
						$fga *= -1;
						$fgp *= -1;
						$tpm *= -1;
						$tpa *= -1;
						$tpp *= -1;
						$ftm *= -1;
						$fta *= -1;
						$ftp *= -1;
						$oreb *= -1;
						$dreb *= -1;
						$treb *= -1;
						$asst *= -1;
						$stl *= -1;
						$blk *= -1;
						$tos *= -1;
						$pf *= -1;
						$pts *= -1;
					  }
					
					else if($j==0)
					 {
					 	$fgm *= -1;
						$fga *= -1;
						$fgp *= -1;
						$tpm *= -1;
						$tpa *= -1;
						$tpp *= -1;
						$ftm *= -1;
						$fta *= -1;
						$ftp *= -1;
						$oreb *= -1;
						$dreb *= -1;
						$treb *= -1;
						$asst *= -1;
						$stl *= -1;
						$blk *= -1;
						$tos *= -1;
						$pf *= -1;
						$pts *= -1;
						
						$statsAway->execute();
						
						$fgm *= -1;
						$fga *= -1;
						$fgp *= -1;
						$tpm *= -1;
						$tpa *= -1;
						$tpp *= -1;
						$ftm *= -1;
						$fta *= -1;
						$ftp *= -1;
						$oreb *= -1;
						$dreb *= -1;
						$treb *= -1;
						$asst *= -1;
						$stl *= -1;
						$blk *= -1;
						$tos *= -1;
						$pf *= -1;
						$pts *= -1;
					  }
		
					//print($team.",".$oreb.",".$dreb.",".$treb.",".$asst.",".$stl.",".$blk.",".$to.",".$pf.",".$pts.",");


					

					//*******************************//
				}
				
				if($names[$homeTeam] == ($n+1))
				{
				    //print("Home Team");
					//******* HOME STATS ***********//
					
					$find->execute();
					$can = $find->fetch();
					
					if($j>0 && ($can[0] == $gm))
 						$updateHome->execute();
 					  
					
					else if($j>0)
						$statsHome->execute();
					  
		
					$team= $homeTeam;
		
					$fgs= explode("-", $home[1]);
					$fgm= ($fgm*($j) + (double) $fgs[0])/($j+1);
					$fga= ($fga*($j) +(double) $fgs[1])/($j+1);
					$fgp= ($fgp*($j) + ((double) $fgs[0] )/ (double) $fgs[1])/($j+1);
		
					$tps= explode("-",$home[2]);
					$tpm= ($tpm*($j) + (double) $tps[0])/($j+1);
					$tpa= ($tpa*($j) + (double) $tps[1])/($j+1);
					$tpp= ($tpp*($j) + ((double) $tps[0])/ (double) $tps[1])/ ($j+1);
		
					$fts= explode("-", $home[3]);
					$ftm= ($ftm*($j) + (double) $fts[0])/($j+1);
					$fta= ($fta*($j) + (double) $fts[1])/($j+1);
					$ftp= ( $ftp*($j) + ((double) $fts[0] )/ (double) $fts[1])/($j+1);
		
					$oreb= ($oreb*($j) + (double) $home[4])/($j+1);
					$dreb= ($dreb*($j) + (double) $home[5])/($j+1);
					$treb= ($treb*($j) + (double) $home[6])/($j+1);
					$asst= ($asst*($j) + (double) $home[7])/($j+1);
					$stl= ($stl*($j) + (double) $home[8])/($j+1);
					$blk= ($blk*($j) + (double) $home[9])/($j+1);
					$tos= ($tos*($j) + (double) $home[10])/($j+1);
					$pf= ($pf*($j) + (double) $home[11])/($j+1);
					$pts= ($pts*($j) + (double) $home[12])/($j+1);
					
					if($j==0 && ($can[0] == $gm))
 						$updateHome->execute();
 					  
					
					else if($j==0)
						$statsHome->execute();
					
		
					//******************************//
				}
		
				//GET RESULT OF GAME
				if(($home[12]-$away[12]) > 0)
					$res= 1;
				else
					$res=-1;
				//print($res);
		
				//print("</br>");
				
				$ins->execute();
			}
		//******************************************************//
	}
	print("DONE WITH SEASON 3");
?>