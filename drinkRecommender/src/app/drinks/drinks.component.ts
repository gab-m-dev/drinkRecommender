import { StringMap } from '@angular/compiler/src/compiler_facade_interface';
import { Component, OnInit } from '@angular/core';
import { Drinks } from 'src/Drinks';
import { GetDrinkService } from '../get-drink.service'

class Drink implements Drinks {

  Name:String;
  Glass:String;
  Category:String;
  Ingrediants: String;
  Alcohol: String;
  Instructions: String;

  constructor(drinkname: String,glass: String,category: String, ingrediants: String, alcohol: String, instructions: String){
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
    console.log('getDrink: called');
    this.getDrinkService.getDrinks().subscribe((returnedDrinks: Array<Drink>) => this.drinks.push(new Drink(returnedDrinks[0].Name,returnedDrinks[0].Glass,returnedDrinks[0].Category,returnedDrinks[0].Ingrediants,returnedDrinks[0].Alcohol,returnedDrinks[0].Instructions)));
    //console.log(this.returnedDrinks)
    //this.drink[0] = new Drink(this.returnedDrinks[0]['Name'],this.returnedDrinks[0]['Glass'],this.returnedDrinks[0]['Category'],this.returnedDrinks[0]['Alcohol'],this.returnedDrinks[0]['Instructions'],this.returnedDrinks[0]['Ingrediants'] );
  }
}
