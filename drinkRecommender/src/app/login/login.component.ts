import { Component, Input, OnInit } from '@angular/core';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

    model: any = {};

  constructor(private authService: AuthService, private router: Router) { }

  ngOnInit(): void {
    if (history.state.logout == true){
      this.deleteToken();
    }
  }

  

  onSubmit(input: any) {
    if (input['username'] && input['password']){
      this.authService.login(input['username'], input['password']).subscribe((res) => this.saveTokenAndRout(res));
    }
  }

  private saveTokenAndRout(authResult: any){

    if(authResult.jwt !== undefined){
      localStorage.setItem('id_token', authResult.jwt);
      this.router.navigate(['/usagedata']);
    } else {
      this.router.navigate(['/login']).then(() => {alert("Username or Password false")})
    }
  }

  private deleteToken(){
    localStorage.clear();
    this.router.navigate(['/']).then(() => {location.reload();});
  }
}
