<?php
//menu template.
 ?>
 <template id="network-menu">
   <nav>
     <ul v-bind:class="menuClass()">
       <li v-for="item in menu.items" v-bind:class="itemClass(item)">
         <a v-if="!item.isvjslink" v-html="item.title" v-bind:href="item.url"></a>
         <router-link v-if="item.isvjslink" v-bind:key="linKey(item)" v-bind:to="relativeUrl(item)" exact></router-link>
      </li>
     </ul>
   </nav>
 </template>
