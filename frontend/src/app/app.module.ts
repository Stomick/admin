import { NgModule } from '@angular/core';
import { BrowserModule, Title } from '@angular/platform-browser';
import { HttpModule } from '@angular/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { TooltipModule } from "ngx-tooltip";

import { ChartsModule } from 'ng2-charts/ng2-charts';
import { FroalaEditorModule } from "ng2-froala-editor/ng2-froala-editor"; 
import { CKEditorModule } from 'ng2-ckeditor';
import { DataTableModule } from "angular2-datatable";
import { ImageUploadModule } from "angular2-image-upload";

import { routing } from './app.routing';
import { AppConfig } from './app.config';


//General
import { AppComponent } from './app.component';
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

//Form elements
import { InputsComponent } from './pages/form-elements/inputs/inputs.component';
import { LayoutsComponent } from './pages/form-elements/layouts/layouts.component';
import { ValidationsComponent } from './pages/form-elements/validations/validations.component';
import { WizardComponent } from './pages/form-elements/wizard/wizard.component';

//Tables
import { BasicTablesComponent } from './pages/tables/basic-tables/basic-tables.component';
import { DynamicTablesComponent } from './pages/tables/dynamic-tables/dynamic-tables.component';

//Editors
import { FroalaComponent } from './pages/editors/froala/froala.component';
import { Ckeditor } from './pages/editors/ckeditor/ckeditor.component';


//Maps
import { AgmCoreModule } from 'angular2-google-maps/core';
import { GoogleMapsComponent } from './pages/maps/google/google-maps.component';
import { VectorMapsComponent } from './pages/maps/vector/vector-maps.component';
import { LeafletMapsComponent } from './pages/maps/leaflet/leaflet-maps.component';


//pages components collection
import { DatamapComponent } from './pages/dashboard/datamap/datamap.component';
import { DynamicChartComponent } from './pages/dashboard/dynamic-chart/dynamic-chart.component';
import { TodoComponent } from './pages/dashboard/todo/todo.component';
import { ChatComponent } from './pages/dashboard/chat/chat.component';
import { FeedComponent } from './pages/dashboard/feed/feed.component';

import { FileUploaderComponent } from './pages/form-elements/inputs/file-uploader/file-uploader.component';
import { ImageUploaderComponent } from './pages/form-elements/inputs/image-uploader/image-uploader.component';
import { MultipleImageUploaderComponent } from './pages/form-elements/inputs/multiple-image-uploader/multiple-image-uploader.component';

//theme components
import { Navbar } from './theme/components/navbar/navbar.component';
import { Footer } from './theme/components/footer/footer.component';
import { Messages } from './theme/components/messages/messages.component';
import { Sidebar } from './theme/components/sidebar/sidebar.component';
import { Breadcrumb } from './theme/components/breadcrumb/breadcrumb.component';
import { BackTop } from './theme/components/back-top/back-top.component';

//directives
import {SlimScroll} from './theme/directives/slim-scroll/slim-scroll.directive';
import {ProgressAnimate} from './theme/directives/progress-animate/progress-animate.directive';
import {Widget} from './theme/directives/widget/widget.directive';
import {LiveTile} from './theme/directives/live-tile/live-tile.directive';
import {Skycon} from './theme/directives/skycon/skycon.directive';
import {Counter} from './theme/directives/counter/counter.directive';
import {DropzoneUpload} from './theme/directives/dropzone/dropzone.directive';

import {AuthGuard} from './pages/login/authGuard/authGuard.service'

// nothification
import {ToastModule} from 'ng2-toastr/ng2-toastr';

//pipes
import {ProfilePicturePipe} from './theme/pipes/profilePicture/profilePicture.pipe';
import {AppPicturePipe} from './theme/pipes/appPicture/appPicture.pipe';
import {SearchPipe} from './theme/pipes/search/search.pipe';
import {MailSearchPipe} from './theme/pipes/search/mail-search.pipe';


//Custom pages
import {FacilitiesListComponent} from './letmesport/facilities-list/facilities-list.component';
import {PaginationsComponent} from './letmesport/paginations/paginations.component';
import {ModalComponent} from './letmesport/facilities-list/modal/modal.component';
import {FacilityEditComponent} from './letmesport/facility-edit/facility-edit.component';
import {PlaygroundComponent} from './letmesport/facility-edit/playground/playground.component';
import {WorktimeComponent} from './letmesport/facility-edit/worktime/worktime.component';
import {PhotoUploadComponent} from './letmesport/facility-edit/photo-upload/photo-upload.component';
import {AdvantagesListComponent} from './letmesport/advantages/advantagesList.component';
import {AdvantagesAddComponent} from './letmesport/advantagesAdd/advantagesAdd.component';
import {ServiceListComponent} from './letmesport/servicesList/servicesList.component';
import {CreateAdvan} from './letmesport/advantagesAdd/createAdvan/createAdvan.component';
import {EditAdvan} from './letmesport/advantagesAdd/editAdvan/editAdvan.component';
import {UploadIcon} from './letmesport/advantagesAdd/uploadIcon/uploadIcon.component';
import {AdminListComponent} from './letmesport/adminList/adminList.component';
import {AdminCreateComponent} from './letmesport/adminList/adminCreateModal/adminCreateModal.component';
import {UserListComponent} from './letmesport/userList/userList.component';
import {EditAdminModalComponent} from './letmesport/facilities-list/editAdminModal/editAdminModal.component';
import {RequisitesComponent} from './letmesport/requisites/requisites.component';
import {TimeTableComponent} from './letmesport/timetable/timetable.component';
import {TimeTableGroundComponent} from './letmesport/timetable/timetableGround/timetableGround.component';
import {BookingsComponent} from './letmesport/timetable/bookings/bookings.component';


@NgModule({
  imports: [
    TooltipModule,
    BrowserModule,
    HttpModule,
    FormsModule,
    ReactiveFormsModule,
    routing,
    ChartsModule,
    FroalaEditorModule,
    CKEditorModule,
    DataTableModule,
    ToastModule.forRoot(),
    ImageUploadModule.forRoot(),
    AgmCoreModule.forRoot({
      apiKey: 'AIzaSyDe_oVpi9eRSN99G4o6TwVjJbFBNr58NxE'
    })
  ],
  declarations: [
    AppComponent,
    PagesComponent,
    BlankComponent,
    Ng2ChartsComponent,
    LoginComponent,
    RegisterComponent,
    PageNotFoundComponent,
    SearchComponent,
    CalendarComponent,
    MailComponent,
    MailListComponent,
    MailComposeComponent,
    MailDetailComponent,
    Navbar,
    Footer,
    Messages,
    Sidebar,
    DashboardComponent,
    DatamapComponent,
    DynamicChartComponent,
    TodoComponent,
    ChatComponent,
    FeedComponent,    
    GoogleMapsComponent,
    VectorMapsComponent,
    LeafletMapsComponent, 
    IconsComponent,
    ButtonsComponent,
    TypographyComponent,
    GridComponent, 
    CardsComponent, 
    TabsAccordionsComponent,
    ComponentsComponent,
    ListGroupComponent, 
    MediaObjectsComponent,
    InputsComponent,
    FileUploaderComponent,
    ImageUploaderComponent,
    MultipleImageUploaderComponent,
    LayoutsComponent,
    ValidationsComponent,
    WizardComponent,
    FroalaComponent,
    Ckeditor,
    BasicTablesComponent,
    DynamicTablesComponent,
    Breadcrumb,
    BackTop,
    SlimScroll,
    ProgressAnimate,
    Widget,
    LiveTile,
    Skycon,
    Counter,
    DropzoneUpload,
    ProfilePicturePipe,
    AppPicturePipe,
    SearchPipe,
    MailSearchPipe,
    FacilitiesListComponent,
    FacilityEditComponent,
    PaginationsComponent,
    ModalComponent,
    CreateAdvan,
    EditAdvan,
    UploadIcon,
    PlaygroundComponent,
    WorktimeComponent,
    PhotoUploadComponent,
    AdvantagesListComponent,
    AdvantagesAddComponent,
    ServiceListComponent,
    AdminListComponent,
    AdminCreateComponent,
    UserListComponent,
    EditAdminModalComponent,
    RequisitesComponent,
    TimeTableComponent,
    TimeTableGroundComponent,
    BookingsComponent
  ],
  providers: [
    Title,
    AppConfig,
    AuthGuard
  ],
  bootstrap: [ AppComponent ]

 
})
export class AppModule {
}