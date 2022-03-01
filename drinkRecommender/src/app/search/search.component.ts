import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms'
import { Router } from '@angular/router';


enum SearchCriteria {
  SimIng = "Similar drinks by ingrediants to:",
  SimInst = "Similar drinks by instructions to:",
  Ing = "Drink by ingrediants:",
  Rec = "Recepie for:",
}

@Component({
  selector: 'app-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.css']
})



export class SearchComponent implements OnInit {

  model: any = {};
  searchQuery = "";
  label = "";




  constructor(
    private router: Router
  ) { }

  ngOnInit(): void {
  }

  onSubmit(input: any) {
    this.parseData(input);
    this.router.navigate(['/drinks'], {state: {data: this.searchQuery, label: this.label}});
  }

  parseData(input: any){
    switch(input['searchCriteria']){
      case 'SimIng':
        this.label = SearchCriteria.SimIng + " " + input['input']
        this.searchQuery = "/" + input['input'] + "?sim=ing"
        break
      case 'SimInst':
        this.label = SearchCriteria.SimInst + " " + input['input']
        this.searchQuery = "/" + input['input'] + "?sim=inst"
        break
      case 'Ing':
        this.label = SearchCriteria.Ing + " " + input['input']
        let ingArray = input['input'].toLowerCase().split(",");
        let ings = "ing[]=" + ingArray[0];
        for (let i = 1; i < ingArray.length; i++){
          ings = ings + "&ing[]=" + ingArray[i]
        }
        this.searchQuery = "/ing?" + ings
        break
      case 'Rec':
        this.label = SearchCriteria.Rec + " " + input['input']
        this.searchQuery = "/rec/" + input['input']
        break
    }

  }
  
}
