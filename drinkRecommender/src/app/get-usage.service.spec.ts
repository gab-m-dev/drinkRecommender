import { TestBed } from '@angular/core/testing';

import { GetUsageService } from './get-usage.service';

describe('GetUsageService', () => {
  let service: GetUsageService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(GetUsageService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
