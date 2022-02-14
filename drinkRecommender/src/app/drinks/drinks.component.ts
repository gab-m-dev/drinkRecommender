import { Component, OnInit } from '@angular/core';
import { waitForAsync } from '@angular/core/testing';
import { Drinks } from 'src/Drinks';
import { GetDrinkService } from '../get-drink.service'

class Drink implements Drinks {

  Name:String;
  Glass:String;
  Category:String;
  Ingrediants: Object;
  Alcohol: String;
  Instructions: String;

  constructor(drinkname: String,glass: String,category: String, ingrediants: Object, alcohol: String, instructions: String){
    this.Name = drinkname;
    this.Glass = glass;
    this.Category = category;
    this.Alcohol = alcohol;
    this.Instructions = instructions;
    this.Ingrediants = ingrediants;
  }
}


@Component({
  selector: 'app-drinks',
  templateUrl: './drinks.component.html',
  styleUrls: ['./drinks.component.css']
})

export class DrinksComponent implements OnInit {

  drinks: Drink[] = [];
  //private returnedDrinks: any[] = [];


  constructor(private getDrinkService: GetDrinkService) {
    //this.drink = new Drink("TestDrink","TestGlass","TestCategory")
   }

  ngOnInit(): void {
    this.getDrink();
  }

  getDrink(): void {
      this.getDrinkService.getDrinks().subscribe((returnedDrinks: Array<Drink>) => { for(let i = 0; i < returnedDrinks.length; i++){
      this.drinks.push(new Drink(returnedDrinks[i].Name,returnedDrinks[i].Glass,returnedDrinks[i].Category, JSON.parse(String(returnedDrinks[i].Ingrediants).replace(/'/g, '"')),returnedDrinks[i].Alcohol,returnedDrinks[i].Instructions))
    }});
  
    //this.drink[0] = new Drink(this.returnedDrinks[0]['Name'],this.returnedDrinks[0]['Glass'],this.returnedDrinks[0]['Category'],this.returnedDrinks[0]['Alcohol'],this.returnedDrinks[0]['Instructions'],this.returnedDrinks[0]['Ingrediants'] );
  }
}
