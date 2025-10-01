import { Routes } from '@angular/router';
import { UsuariosListaComponent } from './module/usuarios/components/usuarios-lista/usuarios-lista.component';

export const routes: Routes = [
  { path: 'usuarios', component: UsuariosListaComponent },
  { path: '', redirectTo: 'usuarios', pathMatch: 'full' },
];
