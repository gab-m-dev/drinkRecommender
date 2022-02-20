import {Injectable} from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { MessageService } from './message.service';

@Injectable({
  providedIn: 'root'
})
export class GetDrinkService {

  constructor(private http: HttpClient, private messageService: MessageService) {
    this.messageService.add('GetDrinkService: made');
   }

  getDrinks(searchQuery: string): Observable<any>{
    this.messageService.add('GetDrinkService: fetched drinks');
    return this.http.get('http://localhost:8888/api-drinks' + searchQuery);
  }
}
