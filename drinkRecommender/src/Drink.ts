export class Drink {

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
