<?php

header ("Content-Type:application/json");

require_once '../util/request.base.php';

class GetCategoriesClass extends ReqBase
{
	
	private $requiredFields = array('registeredId');
	public $dataObj = null;
	
	public $categories = array("Arcades","Art Galleries","Bowling Alleys","Casinos","Comedy Clubs","Gaming Cafes","General Entertainment","Internet Cafes","Movie Theaters","Indie Movie Theaters","Multiplexes","Museums","Art Museums","History Museums","Museums","Planetariums","Science Museums","Music Venues","Concert Halls","Jazz Clubs","Piano Bars","Rock Clubs","Performing Arts Venues","Concert Halls","Dance Studios","Indie","Off Broadway Theaters","Opera Houses","Theaters","Pool Halls","Racetracks","Stadiums","Baseball Stadiums","Basketball Stadiums","Cricket Grounds","Football Stadiums","Hockey Stadiums","Soccer Stadiums","Tracks","Strip Clubs","Theme Parks","Water Parks","Zoos","Aquariums","African Restaurants","American Restaurants","Arepa Restaurants","Argentinian Restaurants","Asian Restaurants","Australian Restaurants","BBQ Joints","Bagel Shops","Bakeries","Brazilian Restaurants","Breakfast Spots","Breweries","Burger Joints","Burrito Places","Cafes","Cajun","Creole Restaurants","Caribbean Restaurants","Chinese Restaurants","Coffee Shops","Cuban Restaurants","Cupcake Shops","Delis","Bodegas","Dessert Shops","Dim Sum Restaurants","Diners","Donut Shops","Dumpling Restaurants","Eastern European Restaurants","Ethiopian Restaurants","Falafel Restaurants","Fast Food Restaurants","Food Courts","Food Trucks","French Restaurants","Fried Chicken Joints","Gastropubs","German Restaurants","Gluten-free Restaurants","Greek Restaurants","Hot Dog Joints","Ice Cream Shops","Indian Restaurants","Italian Restaurants","Japanese Restaurants","Juice Bars","Korean Restaurants","Latin American Restaurants","Mac & Cheese Joints","Malaysian Restaurants","Mediterranean Restaurants","Mexican Restaurants","Middle Eastern Restaurants","Molecular Gastronomy Restaurants","Moroccan Restaurants","New American Restaurants","Pizza Places","Ramen","Noodle House","Restaurants","Salad Shop","Sandwich Places","Scandinavian Restaurants","Seafood Restaurants","Snack Places","Soup Places","South American Restaurants","Southern","Soul Food Restaurants","Spanish Restaurants","Paella Restaurants","Steakhouses","Sushi Restaurants","Swiss Restaurants","Taco Places","Tapas Restaurants","Tea Rooms","Thai Restaurants","Vegetarian","Vegan Restaurants","Vietnamese Restaurants","Wineries","Wings Joints","Vineyard ","Bars","Beer Gardens","Breweries","Cocktails Bars","Dive Bars","Gay Bars","Hookah Bars","Hotel Bars","Karaoke Bars","Lounges","Music Venues","Concert Halls","Jazz Clubs","Piano Bars","Rock Clubs","Nightclubs","Other Nightlife","Pubs","Sake Bars","Speakeasies","Sports Bars","Strip Clubs","Whisky Bars","Wine Bars");
	
	function GetCategoriesClass()
	{
		parent::__construct();
	}
	
	function GetCategoriesGo()
	{
		$this->dataObj = $this->genDataObj();
		
		$this->checkProperties($this->requiredFields, $this->dataObj);
		$me = $this->checkRegUserId($this->dataObj);
				
		$data = json_encode($this->categories);
		
		echo $data;
	}
}

?>