import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Drink } from 'src/Drink';



@Component({
  selector: 'app-drink-detail',
  templateUrl: './drink-detail.component.html',
  styleUrls: ['./drink-detail.component.css']
})


export class DrinkDetailComponent implements OnInit {
  @Input() drink?: Drink; 

  constructor(private route: ActivatedRoute,) {
   }

  ngOnInit(): void {
    this.drink = history.state.data
  }

}
