import { Component, OnInit } from '@angular/core';
import { LoginComponent } from './login/login.component';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit{
  title = 'gabor\'s drinkRecommender';
  loginState = false;
  loginText = "Login";

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
}
