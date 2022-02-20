export class Drink {

  Name:String;
  Glass:String;
  Category:String;
  Ingrediants: Object;
  Alcohol: String;
  Instructions: String;
  Value_Ingrediants: String | undefined;
  Value_Instructions: String | undefined;

  constructor(drinkname: String,glass: String,category: String, ingrediants: Object, alcohol: String, instructions: String, similarity_ing: String | undefined =  undefined, similarity_inst: String | undefined = undefined){
    this.Name = drinkname;
    this.Glass = glass;
    this.Category = category;
    this.Alcohol = alcohol;
    this.Instructions = instructions;
    this.Ingrediants = ingrediants;
    this.Value_Ingrediants = similarity_ing;
    this.Value_Instructions = similarity_inst;
  }
}
