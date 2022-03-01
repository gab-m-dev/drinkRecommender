import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { DrinkDetailComponent } from './drink-detail/drink-detail.component';
import { DrinksComponent } from './drinks/drinks.component';
import { LoginComponent } from './login/login.component';
import { SearchComponent } from './search/search.component';
import { UsageComponent } from './usage/usage.component';

const routes: Routes = [
  { path: '', component: SearchComponent },
  { path: 'detail', component: DrinkDetailComponent },
  { path: 'drinks', component: DrinksComponent },
  { path: 'search', component: SearchComponent },
  { path: 'login', component: LoginComponent },
  { path: 'usage', component: UsageComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
