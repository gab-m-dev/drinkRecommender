import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class GetUsageService {

  constructor(private http: HttpClient) { }

  getUsage(): Observable<any>{
      return this.http.get('https://api-drinks.gabormuff.info/usagedata');
  }
}
