import { finalize } from 'rxjs';
import { LoadingComponent } from '../../../../components/loading/loading.component';
import { FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Component, OnInit, OnDestroy, AfterViewInit, inject } from '@angular/core';
import { Usuario } from '../../model/usuario.model';
import { UsuariosService } from '../../service/usuario.service';
import { UsuariosFormularioComponent } from '../usuarios-formulario/usuarios-formulario.component';
import { MensajeService } from '../../service/mensaje.service';

@Component({
  selector: 'app-usuarios-lista',
  imports: [CommonModule, UsuariosFormularioComponent, ReactiveFormsModule, LoadingComponent],
  templateUrl: './usuarios-lista.component.html',
  styleUrls: ['./usuarios-lista.component.css'],
})
export class UsuariosListaComponent implements OnInit, AfterViewInit, OnDestroy {
  protected usuarios: Usuario[] = [];
  protected usuarioSeleccionado: Usuario | null = null;
  protected usuarioAEliminar: Usuario | null = null;
  protected formularioBuscar: FormGroup;
  private mensajeService = inject(MensajeService);
  private usuariosService = inject(UsuariosService);
  private fb = inject(FormBuilder);
  protected cargando = false;
  private toolTips: any[] = []; //toca crear este objeto para poder hacer ToolTips ya que con solo la libreria no funcionaba en todos los items

  constructor() {
    this.formularioBuscar = this.fb.group({
      nombres: [''],
      apellidos: [''],
      telefono: [''],
      correo: [''],
    });
  }

  ngOnInit(): void {
    this.cargarUsuarios();
  }

  //implementamos este metodo para asegurarnos que esten los tooltips cuando cargue el DOM
  ngAfterViewInit(): void {
    this.inicializarTooltips();
  }

  //asi evitamos que salgan los Tooltips en lugares inesperados
  ngOnDestroy(): void {
    this.destruirTooltips();
  }

  inicializarTooltips() {
    this.destruirTooltips(); //asegurar que no existan tooltips inecesarios

    //guardamos todos los elementos que tienen el atributo para despues mapearlo cada item con el tooltip de boostrap
    const listaTooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    this.toolTips = listaTooltips.map(function (tooltipTriggerEl) {
      return new (window as any).bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  //eliminamos la instancia de la clase de boostrap para tooltips
  destruirTooltips() {
    this.toolTips.forEach((tooltip) => tooltip.dispose());
  }

  cargarUsuarios() {
    this.cargando = true;
    this.usuariosService
      .listar()
      .pipe(finalize(() => (this.cargando = false))) //para poder parar la pantalla de cargar
      .subscribe((data) => {
        this.usuarios = data;
        setTimeout(() => this.inicializarTooltips(), 0); //cuando cargue los datos del backend ahi si creamos los tooltips
      });
    this.usuarioSeleccionado = null; //agregamos esto para evitar selecciones de datos inecesario y evitar errores en formulario
  }

  buscar() {
    const parametros = this.formularioBuscar.value;

    //verificamos si estamos pasando parametros vacios por que si es asi debe mostrarme todos los datos
    const datosExistente = Object.values(parametros).every((x) => x === null || x === '');
    if (datosExistente) {
      this.cargarUsuarios();
    } else {
      this.cargando = true;
      //buscamos los datos que coincidan en la bd con los parametros y lo guardamos en memoria
      this.usuariosService
        .buscar(parametros)
        .pipe(finalize(() => (this.cargando = false)))
        .subscribe((data) => {
          this.usuarios = data;
          if(data.length === 0){
            this.mensajeService.error("No se encontraron coincidencias");
          }
          setTimeout(() => this.inicializarTooltips(), 0);
        });
    }
  }

  limpiarBusqueda() {
    this.formularioBuscar.reset({
      nombres: '',
      apellidos: '',
      telefono: '',
      correo: '',
    });
    this.cargarUsuarios();
  }

  //con esto evitamos el error de datos si cerramos el modal y lo volvemos a abrir
  modalCerrado() {
    this.usuarioSeleccionado = null;
  }

  //con esto pasamos los datos del usuario al modal y lo abrimos
  editar(usuario: Usuario) {
    this.usuarioSeleccionado = usuario;
    const modal = new (window as any).bootstrap.Modal(document.getElementById('crearUsuarioModal'));
    modal.show();
  }

  //con esto le pasamos el usuario que vamos a eliminar para mirar en el modal el nombre y recuperar el id
  eliminar(usuario: Usuario) {
    this.usuarioAEliminar = usuario;
    const modal = new (window as any).bootstrap.Modal(
      document.getElementById('confirmarEliminarModal')
    );
    modal.show();
  }

  //accion ya relacionado con la eliminacion en la bd
  confirmarEliminacion() {
    if (this.usuarioAEliminar) {
      this.cargando = true;
      this.usuariosService
        .eliminar(this.usuarioAEliminar.id!)
        .pipe(finalize(() => (this.cargando = false)))
        .subscribe(() => {
          this.mensajeService.success('Usuario eliminado con éxito');
          this.cargarUsuarios();
          const modal = document.getElementById('confirmarEliminarModal');
          if (modal) {
            const modalInstance = (window as any).bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
          }
        });
    }
  }
}
