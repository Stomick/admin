import { ModuleWithProviders }  from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

//General
import { PagesComponent } from './pages/pages.component';
import { DashboardComponent } from './pages/dashboard/dashboard.component';
import { CalendarComponent } from './pages/calendar/calendar.component';

//Charts
import { Ng2ChartsComponent } from './pages/charts/ng2-charts/ng2-charts.component';

//Pages
import { BlankComponent } from './pages/blank/blank.component';
import { LoginComponent } from './pages/login/login.component';
import { RegisterComponent } from './pages/register/register.component';
import { PageNotFoundComponent } from './pages/error/pagenotfound.component';
import { SearchComponent } from './pages/search/search.component';

//Inbox
import { MailComponent } from './pages/mail/mail.component';
import { MailListComponent } from './pages/mail/mail-list/mail-list.component';
import { MailComposeComponent } from './pages/mail/mail-compose/mail-compose.component';
import { MailDetailComponent } from './pages/mail/mail-detail/mail-detail.component';

//Maps
import { GoogleMapsComponent } from './pages/maps/google/google-maps.component';
import { VectorMapsComponent } from './pages/maps/vector/vector-maps.component';
import { LeafletMapsComponent } from './pages/maps/leaflet/leaflet-maps.component';

//UI
import { IconsComponent } from './pages/ui/icons/icons.component';
import { ButtonsComponent } from './pages/ui/buttons/buttons.component';
import { TypographyComponent } from './pages/ui/typography/typography.component';
import { GridComponent } from './pages/ui/grid/grid.component';
import { CardsComponent } from './pages/ui/cards/cards.component';
import { TabsAccordionsComponent } from './pages/ui/tabs-accordions/tabs-accordions.component';
import { ComponentsComponent } from './pages/ui/components/components.component';
import { ListGroupComponent } from './pages/ui/list-group/list-group.component';
import { MediaObjectsComponent } from './pages/ui/media-objects/media-objects.component';

//Editors
import { FroalaComponent } from './pages/editors/froala/froala.component';
import { Ckeditor } from './pages/editors/ckeditor/ckeditor.component';

//Tables
import { BasicTablesComponent } from './pages/tables/basic-tables/basic-tables.component';
import { DynamicTablesComponent } from './pages/tables/dynamic-tables/dynamic-tables.component';

//Form elements
import { InputsComponent } from './pages/form-elements/inputs/inputs.component';
import { LayoutsComponent } from './pages/form-elements/layouts/layouts.component';
import { ValidationsComponent } from './pages/form-elements/validations/validations.component';
import { WizardComponent } from './pages/form-elements/wizard/wizard.component';

//Custom page
import {FacilitiesListComponent} from './letmesport/facilities-list/facilities-list.component';
import {FacilityEditComponent} from "./letmesport/facility-edit/facility-edit.component";
import {AdvantagesAddComponent} from "./letmesport/advantagesAdd/advantagesAdd.component";
import {AdminListComponent} from "./letmesport/adminList/adminList.component";
import {UserListComponent} from "./letmesport/userList/userList.component";
import {RequisitesComponent} from "./letmesport/requisites/requisites.component";
import {TimeTableComponent} from "./letmesport/timetable/timetable.component";

import {AuthGuard} from './pages/login/authGuard/authGuard.service'

const appRoutes: Routes = [
  {
    path: '',
    redirectTo: 'login/',
    pathMatch: 'full',
  },

  {
    path: 'pages',
    component: PagesComponent,
    canActivate: [AuthGuard],
    data: {roles: ['super-admin', 'admin']},
    children: [
      {
        path: '',
        redirectTo: '/pages/facilities-list',
        pathMatch: 'full'
      },
      {
        path: 'facilities-list',
        component: FacilitiesListComponent,
        data:{
          title: 'Список объектов'
        },
      },
        {
        path: 'user-booking',
        component: FacilitiesListComponent,
        data:{
          title: 'Бронирования'
        },
      },
      {
        path: 'facility-edit/:id',
        component: FacilityEditComponent,
        data:{
          title: 'Редактирование объекта'
        }
      },
      {
        path: 'requisites/:id',
        component: RequisitesComponent,
        data:{
          title: 'Реквизиты'
        }
      },
    ]
  },

  {
    path: 'pages',
    component: PagesComponent,
    canActivate: [AuthGuard],
    data: {roles: ['super-admin']},
    children : [
      {
        path: '',
        redirectTo: '/pages/facilities-list',
        pathMatch: 'full'
      },
      {
        path: 'admin-list',
        component: AdminListComponent,
        data:{
          title: 'Список администраторов'
        }
      },
      {
        path: 'user-list',
        component: UserListComponent,
        data:{
          title: 'Пользователи приложения'
        }
      },
      {
        path: 'advantages',
        component: AdvantagesAddComponent,
        data:{
          title: 'Преимущества',
        }
      },
    ]
  },

  {
    path: 'pages',
    component: PagesComponent,
    canActivate: [AuthGuard],
    data: {roles: ['admin']},
    children: [
      {
        path: '',
        redirectTo: '/pages/facilities-list',
        pathMatch: 'full'
      },
      {
        path: 'timetable/:id',
        component: TimeTableComponent,
        data:{
          title: 'Расписание и бронирование'
        }
      },
    ]
  },

  {
    path: 'login',
    component: LoginComponent
  },

  {
    path: '**',
    component: LoginComponent
  }
];

export const routing: ModuleWithProviders = RouterModule.forRoot(appRoutes, { useHash: true });

//export const routing: ModuleWithProviders = RouterModule.forRoot(appRoutes);