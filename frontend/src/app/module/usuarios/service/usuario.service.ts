import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { Usuario } from '../model/usuario.model';
import { MensajeService } from './mensaje.service';

@Injectable({
  providedIn: 'root',
})
export class UsuariosService {
  private baseUrl = 'http://localhost:8080/usuarios';
  private http = inject(HttpClient);
  private mensajeService = inject(MensajeService);

  listar(): Observable<Usuario[]> {
    return this.http.get<Usuario[]>(this.baseUrl).pipe(
      catchError(err => {
        this.mensajeService.error(err.error?.error || 'Error al cargar la lista de usuarios');
        return throwError(() => err);
      })
    );
  }

  crear(usuario: Usuario): Observable<any> {
    return this.http.post(this.baseUrl, usuario).pipe(
      catchError(err => {
        this.mensajeService.error(err.error?.error || 'Error al crear el nuevo usuario');
        return throwError(() => err);
      })
    );
  }

  editar(id: number, usuario: Usuario): Observable<any> {
    return this.http.put(`${this.baseUrl}/${id}`, usuario).pipe(
      catchError(err => {
        this.mensajeService.error(err.error?.error || 'Error al actualizar el usuario');
        return throwError(() => err);
      })
    );
  }

  eliminar(id: number): Observable<any> {
    return this.http.delete(`${this.baseUrl}/${id}`).pipe(
      catchError(err => {
        this.mensajeService.error(err.error?.error || 'Error al eliminar el usuario');
        return throwError(() => err);
      })
    );
  }

  buscar(params: any): Observable<Usuario[]> {
    return this.http.get<Usuario[]>(`${this.baseUrl}/buscar`, { params }).pipe(
      catchError(err => {
        this.mensajeService.error(err.error?.error || 'Error al realizar la búsqueda');
        return throwError(() => err);
      })
    );
  }
}