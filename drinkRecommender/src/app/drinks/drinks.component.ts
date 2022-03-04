import { Component, Input, OnInit } from '@angular/core';
import { waitForAsync } from '@angular/core/testing';
import { Drink } from 'src/Drink';
import { GetDrinkService } from '../get-drink.service'



@Component({
  selector: 'app-drinks',
  templateUrl: './drinks.component.html',
  styleUrls: ['./drinks.component.css']
})

export class DrinksComponent implements OnInit {

  @Input() searchQuery?: string;
  @Input() label?: string; 
  drinks: Drink[] = [];


  constructor(private getDrinkService: GetDrinkService) {
   }

  ngOnInit(): void {
    this.searchQuery = history.state.data
    this.label = history.state.label
    this.getDrink();
  }

  getDrink(): void {

    if(this.searchQuery){
      this.getDrinkService.getDrinks(this.searchQuery).subscribe((returnedDrinks: Array<Drink>) => { for(let i = 0; i < returnedDrinks.length; i++){
        this.drinks.push(new Drink(returnedDrinks[i].Name,returnedDrinks[i].Glass,returnedDrinks[i].Category, JSON.parse(String(returnedDrinks[i].Ingrediants).replace(/'/g, '"')),returnedDrinks[i].Alcohol,returnedDrinks[i].Instructions, returnedDrinks[i].Value_Ingrediants, returnedDrinks[i].Value_Instructions))
      }});
    } else {
      this.getDrinkService.getDrinks("").subscribe((returnedDrinks: Array<Drink>) => { for(let i = 0; i < returnedDrinks.length; i++){
        this.drinks.push(new Drink(returnedDrinks[i].Name,returnedDrinks[i].Glass,returnedDrinks[i].Category, JSON.parse(String(returnedDrinks[i].Ingrediants).replace(/'/g, '"')),returnedDrinks[i].Alcohol,returnedDrinks[i].Instructions, returnedDrinks[i].Value_Ingrediants, returnedDrinks[i].Value_Instructions))
      }});
    }
      
  }
}
