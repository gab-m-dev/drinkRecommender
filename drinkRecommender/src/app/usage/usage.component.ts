import { Component, OnInit } from '@angular/core';
import { Usage } from 'src/Usage';
import { GetUsageService } from '../get-usage.service'
import { Router } from '@angular/router';

@Component({
  selector: 'app-usage',
  templateUrl: './usage.component.html',
  styleUrls: ['./usage.component.css']
})
export class UsageComponent implements OnInit {
  
  usage?: Usage;

  

  constructor(private getUsageService: GetUsageService, private router: Router) { }

  ngOnInit(): void {
    if(localStorage.getItem('id_token')){
      this.getUsage();
    } else{
      this.router.navigate(['/login']).then(() => {alert("Please Log in to access Usage Data!")});
    }
    
  }

  getUsage(): void{
    this.getUsageService.getUsage().subscribe(
      (response) => {this.usage = new Usage(response[0]['similarDrinks'], response[0]['searchedDrinks']);}, 
      (error) => {this.router.navigate(['/login'], {state: {logout: true}}).then(() => { alert("Session expired. Please log in again.")});
    });
  }
}
