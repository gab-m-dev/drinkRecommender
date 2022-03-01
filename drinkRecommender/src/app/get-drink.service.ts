import {Injectable} from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class GetDrinkService {

  constructor(private http: HttpClient) {
   }

  getDrinks(searchQuery: string): Observable<any>{
    return this.http.get('http://localhost:8888/api-drinks' + searchQuery);
  }
}
