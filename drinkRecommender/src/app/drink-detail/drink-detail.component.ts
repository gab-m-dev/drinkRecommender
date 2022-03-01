import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Drink } from 'src/Drink';
import {Location} from '@angular/common';



@Component({
  selector: 'app-drink-detail',
  templateUrl: './drink-detail.component.html',
  styleUrls: ['./drink-detail.component.css']
})


export class DrinkDetailComponent implements OnInit {
  @Input() drink?: Drink; 

  constructor(private route: ActivatedRoute,private location: Location) {
   }

  ngOnInit(): void {
    this.drink = history.state.data
  }

  back(): void {
    this.location.back();
  }

}
