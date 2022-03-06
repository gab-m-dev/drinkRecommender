import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  constructor(private http: HttpClient) {
   }

  login(username: string, password: string): Observable<any>{
    return this.http.post<any>('url', {username: username, password: password});
  }
}
