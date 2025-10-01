import { Component } from '@angular/core';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';

//esta es la pagina de carga utilizada para mejorar la experiencia del usuario al
//momento de mandar solicitudes al backend
@Component({
  selector: 'app-loading',
  template: `
    <div class="loading-overlay">
      <mat-spinner></mat-spinner>
    </div>
  `,
  styles: [`
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
  `],
  imports: [MatProgressSpinnerModule],
})
export class LoadingComponent {}
