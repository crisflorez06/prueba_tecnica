import { Component, signal } from '@angular/core';

import { UsuariosListaComponent } from "./module/usuarios/components/usuarios-lista/usuarios-lista.component";

@Component({
  selector: 'app-root',
  imports: [UsuariosListaComponent],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App {
  protected readonly title = signal('frontend');
}
