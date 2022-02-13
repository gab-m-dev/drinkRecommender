import { TestBed } from '@angular/core/testing';

import { GetDrinkService } from './get-drink.service';

describe('GetDrinkServiceService', () => {
  let service: GetDrinkService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(GetDrinkService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
