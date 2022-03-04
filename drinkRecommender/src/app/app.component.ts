import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit{
  title = 'gabor\'s drinkRecommender';
  loginState = false;
  loginText = "Login";

  constructor(private router: Router){}

  ngOnInit(): void {
      this.loggedIn;
  }

  loggedIn(){
    if (localStorage.getItem('id_token')){
      return true;
    }
    else{
      return false;
    }
  }

  drinksOnClick(){
    this.router.navigate(['/drinks']).then(() => {location.reload();});
  }
}
