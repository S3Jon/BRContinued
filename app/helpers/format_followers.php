<?php 
function formatFollowers($followersNum)
{
	if ($followersNum == 1)
	{
		return $followersNum . " Seguidor";
	}
	else
	{
		return $followersNum . " Seguidores";
	}
}
